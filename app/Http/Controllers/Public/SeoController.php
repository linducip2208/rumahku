<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\View\View;

class SeoController extends Controller
{
    public function landing(string $listing, string $type, string $city): View
    {
        $validTypes = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];
        $validListings = ['jual', 'sewa'];

        if (! in_array(strtolower($listing), $validListings, true) || ! in_array(strtolower($type), $validTypes, true)) {
            abort(404);
        }

        $listing = strtolower($listing);
        $type = strtolower($type);
        $citySlug = strtolower($city);
        $cityName = ucwords(str_replace('-', ' ', $citySlug));

        $properties = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->where('type', $type)
            ->where('listing_status', $listing)
            ->where('city', 'like', '%'.$cityName.'%')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $listingLabel = $listing === 'jual' ? 'Jual' : 'Sewa';
        $typeLabel = ucfirst($type);
        $heading = "{$listingLabel} {$typeLabel} di {$cityName}";
        $metaTitle = "{$heading} — Harga Terbaru & Terlengkap | Rumah123";
        $metaDesc = "Cari {$listingLabel} {$typeLabel} di {$cityName}. Temukan pilihan properti {$type} {$listing} terbaik di {$cityName} dengan harga terbaru, foto lengkap, dan agen terverifikasi di Rumah123.";
        $canonical = route('seo.landing', ['listing' => $listing, 'type' => $type, 'city' => $citySlug]);

        return view('pages.seo-landing', [
            'properties' => $properties,
            'listing' => $listing,
            'type' => $type,
            'citySlug' => $citySlug,
            'cityName' => $cityName,
            'heading' => $heading,
            'metaTitle' => $metaTitle,
            'metaDesc' => $metaDesc,
            'canonical' => $canonical,
        ]);
    }
}
