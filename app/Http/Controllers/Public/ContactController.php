<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Services\Security\TurnstileValidator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact', [
            'phone' => Setting::get('landing.contact_phone', ''),
            'email' => Setting::get('landing.contact_email', ''),
            'address' => Setting::get('landing.contact_address', ''),
        ]);
    }

    public function store(Request $request, TurnstileValidator $captcha): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if (! $captcha->passes($request->input('cf-turnstile-response'), $request->ip())) {
            return back()->withErrors(['captcha' => 'Verifikasi keamanan gagal. Silakan coba lagi.'])->withInput();
        }

        ContactMessage::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim. Tim kami akan segera menghubungi Anda.');
    }
}
