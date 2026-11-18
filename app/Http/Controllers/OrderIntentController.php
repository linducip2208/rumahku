<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderIntentController extends Controller
{
    /**
     * Simpan niat order paket milik guest sebelum login.
     *
     * Guest yang klik "Pilih paket" di /harga akan diarahkan ke login
     * dan data POST asli pasti hilang (redirect intended hanya untuk GET).
     * Endpoint ini menyimpan niat ke session lalu mengarahkan ke login,
     * sehingga LoginController bisa meneruskannya menjadi order pending.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'package_slug' => ['required', 'string', 'exists:packages,slug'],
        ]);

        $request->session()->put('order_intent', [
            'property_id' => $validated['property_id'],
            'package_slug' => $validated['package_slug'],
        ]);

        return redirect()->route('login')->with(
            'info',
            'Silakan masuk dulu — paket pilihanmu akan diproses otomatis setelah login.'
        );
    }
}
