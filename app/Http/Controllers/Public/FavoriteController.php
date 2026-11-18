<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    public function toggle(Property $property): RedirectResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return redirect()->route('login');
        }

        $existing = Favorite::query()
            ->where('user_id', $userId)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Dihapus dari favorit.');
        }

        if ($property->published_at === null || $property->sold_at !== null) {
            return back()->withErrors(['property_id' => 'Properti tidak tersedia untuk difavoritkan.']);
        }

        Favorite::create([
            'user_id' => $userId,
            'property_id' => $property->id,
        ]);

        return back()->with('success', 'Ditambahkan ke favorit.');
    }
}
