<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use App\Support\NotifiesNewLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
        ]);

        $property = isset($validated['property_id'])
            ? Property::find($validated['property_id'])
            : Property::whereNotNull('published_at')->latest()->first();

        if (! $property) {
            return back()->withErrors(['property_id' => 'Belum ada properti tersedia untuk dihubungi.'])->withInput();
        }

        $lead = Lead::create([
            'property_id' => $property->id,
            'agent_user_id' => $property->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? null,
            'source' => 'web',
        ]);

        NotifiesNewLead::send($property, $lead);

        return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim. Agen kami akan segera menghubungi Anda.');
    }
}
