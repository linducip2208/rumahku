<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->where('featured', true)
            ->latest()
            ->take(8)
            ->get();

        $cities = Property::query()
            ->whereNotNull('published_at')
            ->selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        return view('pages.home', [
            'featured' => $featured,
            'cities' => $cities,
        ]);
    }
}
