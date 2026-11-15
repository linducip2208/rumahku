<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\KprApplication;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KprController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
        ]);

        $property = Property::findOrFail($request->input('property_id'));

        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'dp_amount' => ['required', 'integer', 'min:0', 'max:'.$property->price],
            'tenor_months' => ['required', 'integer', Rule::in(KprApplication::TENORS)],
            'bank_preference' => ['nullable', 'string', Rule::in(KprApplication::BANKS)],
            'monthly_income' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        KprApplication::create([
            'user_id' => $request->user()?->id,
            'property_id' => $property->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'dp_amount' => $validated['dp_amount'],
            'tenor_months' => $validated['tenor_months'],
            'bank_preference' => $validated['bank_preference'] ?? null,
            'monthly_income' => $validated['monthly_income'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Pengajuan KPR berhasil dikirim. Tim kami akan segera menghubungi Anda.');
    }
}
