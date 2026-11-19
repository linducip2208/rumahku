<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\User;
use App\Services\Security\TurnstileValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request, TurnstileValidator $captcha): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'role' => ['nullable', 'string', 'in:owner,agent'],
        ]);

        if (! $captcha->passes($request->input('cf-turnstile-response'), $request->ip())) {
            return back()->withErrors(['captcha' => 'Verifikasi keamanan gagal. Silakan coba lagi.'])->withInput();
        }

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $role = $validated['role'] ?? 'agent';

        $user->forceFill(['role' => $role])->save();

        if (Role::query()->where('name', $role)->exists() && ! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        if ($role === 'agent') {
            AgentProfile::query()->create([
                'user_id' => $user->id,
                'phone' => '',
                'wa' => '',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }
}
