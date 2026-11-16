<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Support\NotifiesNewLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if ($request->user() && $request->user()->tokenCant('leads:write')) {
            abort(403, 'Token tidak memiliki ability leads:write.');
        }

        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $property = Property::query()->findOrFail($validated['property_id']);

        $lead = $property->leads()->create([
            'agent_user_id' => $property->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? null,
            'source' => 'api',
        ]);

        NotifiesNewLead::send($property, $lead);

        return response()->json([
            'message' => 'Lead berhasil dikirim.',
            'id' => $lead->id,
        ], 201);
    }
}
