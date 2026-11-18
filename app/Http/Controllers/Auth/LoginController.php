<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $intentResponse = $this->fulfillOrderIntent($request);

            if ($intentResponse) {
                return $intentResponse;
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Teruskan niat order guest (lihat OrderIntentController) menjadi
     * order pending setelah login. Aturan sama dengan OrderController:
     * milik sendiri + tanpa duplikat pending. Intent selalu dihapus
     * dari session agar tidak diproses dua kali.
     */
    private function fulfillOrderIntent(Request $request): ?RedirectResponse
    {
        $intent = $request->session()->pull('order_intent');

        if (! is_array($intent)) {
            return null;
        }

        $property = Property::query()->find($intent['property_id'] ?? null);
        $package = Package::query()->where('slug', $intent['package_slug'] ?? null)->first();

        if (! $property || ! $package) {
            return redirect()->route('pricing')->withErrors([
                'property_id' => 'Paket pilihanmu sudah tidak tersedia.',
            ]);
        }

        if ((int) $property->user_id !== (int) $request->user()->id) {
            return redirect()->route('pricing')->withErrors([
                'property_id' => 'Properti tersebut bukan milik akun ini.',
            ]);
        }

        $duplicate = PropertyOrder::query()
            ->where('user_id', $request->user()->id)
            ->where('property_id', $property->id)
            ->where('status', 'pending')
            ->exists();

        if (! $duplicate) {
            PropertyOrder::query()->create([
                'user_id' => $request->user()->id,
                'property_id' => $property->id,
                'package_slug' => $package->slug,
                'amount' => $package->price,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('agent.orders.index')->with(
            'success',
            'Order paket dibuat, menunggu verifikasi pembayaran admin.'
        );
    }
}
