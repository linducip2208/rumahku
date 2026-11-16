<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Project;
use App\Models\Property;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->where('featured', true)
            ->latest()
            ->take(8)
            ->get();

        $cities = Property::query()
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $latest = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->latest()
            ->take(8)
            ->get();

        $categoryDefs = [
            ['label' => 'Rumah', 'type' => 'rumah'],
            ['label' => 'Apartemen', 'type' => 'apartemen'],
            ['label' => 'Tanah', 'type' => 'tanah'],
            ['label' => 'Villa', 'type' => 'villa'],
            ['label' => 'Ruko', 'type' => 'ruko'],
            ['label' => 'Kost', 'type' => 'kost'],
            ['label' => 'Gedung Komersial', 'type' => 'gedung'],
            ['label' => 'Gudang', 'type' => 'gudang'],
        ];

        $typeCounts = Property::query()
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $categories = array_map(fn (array $def): array => [
            'label' => $def['label'],
            'type' => $def['type'],
            'count' => (int) ($typeCounts[$def['type']] ?? 0),
            'empty' => ((int) ($typeCounts[$def['type']] ?? 0)) === 0,
        ], $categoryDefs);

        $collectionDefs = [
            ['name' => 'Rumah Mewah', 'type' => 'rumah', 'min_price' => 2000000000],
            ['name' => 'Villa Eksklusif', 'type' => 'villa', 'min_price' => 2000000000],
            ['name' => 'Modern Minimalis', 'type' => 'apartemen', 'min_price' => 500000000],
            ['name' => 'Tropical Living', 'type' => 'villa', 'min_price' => null],
            ['name' => 'Apartemen Premium', 'type' => 'apartemen', 'min_price' => 1000000000],
        ];

        $collections = [];
        foreach ($collectionDefs as $def) {
            $query = Property::query()
                ->whereNotNull('published_at')
                ->whereNull('sold_at')
                ->where('type', $def['type']);

            if ($def['min_price'] !== null) {
                $query->where('price', '>=', $def['min_price']);
            }

            $count = $query->count();

            if ($count > 0) {
                $collections[] = [
                    'name' => $def['name'],
                    'type' => $def['type'],
                    'min_price' => $def['min_price'],
                    'count' => $count,
                ];
            }
        }

        $agents = User::query()
            ->whereHas('agentProfile', fn ($profile): mixed => $profile->whereNotNull('verified_at'))
            ->with('agentProfile')
            ->withCount(['properties as published_properties_count' => fn ($query): mixed => $query->whereNotNull('published_at')->whereNull('sold_at')])
            ->take(6)
            ->get();

        $articles = Article::query()
            ->latest()
            ->take(6)
            ->get();

        $projects = Project::query()->latest()->take(6)->get();

        $landingDefaults = [
            'hero_title' => 'Temukan Rumah Impianmu dengan Mudah',
            'hero_subtitle' => 'Jelajahi properti dijual & disewa di seluruh Indonesia — rumah, apartemen, ruko, tanah, dan villa pilihan.',
            'hero_image' => 'landing/hero.jpg',
            'show_trust' => '1',
            'show_featured' => '1',
            'show_categories' => '1',
            'show_collections' => '1',
            'show_cities' => '1',
            'show_latest' => '1',
            'show_agents' => '1',
            'show_seller' => '1',
            'show_articles' => '1',
            'footer_about' => 'Rumah123 — Portal properti Indonesia: cari rumah, apartemen, tanah, ruko, villa dijual & disewa.',
            'social_facebook' => '',
            'social_instagram' => '',
            'social_youtube' => '',
            'social_tiktok' => '',
            'contact_phone' => '',
            'contact_email' => '',
            'contact_address' => '',
        ];

        $landing = [];
        foreach ($landingDefaults as $key => $default) {
            $landing[$key] = Setting::get('landing.'.$key, $default);
        }

        return view('pages.home', [
            'featured' => $featured,
            'cities' => $cities,
            'latest' => $latest,
            'categories' => $categories,
            'collections' => $collections,
            'agents' => $agents,
            'articles' => $articles,
            'projects' => $projects,
            'landing' => $landing,
        ]);
    }
}
