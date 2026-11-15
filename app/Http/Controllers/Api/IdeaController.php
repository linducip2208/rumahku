<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeatureIdea;
use App\Services\Ideas\IdeaBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function features(): JsonResponse
    {
        return response()->json(['data' => IdeaBank::FEATURES]);
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'feature' => ['nullable', 'string', 'in:'.implode(',', IdeaBank::keys())],
            'mechanism' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = FeatureIdea::query()->orderBy('slot');

        if (! empty($validated['feature'])) {
            $query->where('feature_key', $validated['feature']);
        }

        if (! empty($validated['mechanism'])) {
            $query->where('mechanism', $validated['mechanism']);
        }

        return response()->json($query->paginate($validated['per_page'] ?? 20));
    }
}
