<?php

namespace App\Http\Controllers;

use App\Models\AgentProfile;
use App\Models\Article;
use App\Models\Auction;
use App\Models\Project;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = Cache::remember('sitemap.urls', 3600, function (): array {
            $urls = [];

            $static = [
                ['route' => 'home', 'freq' => 'daily', 'priority' => '1.0'],
                ['route' => 'properties.index', 'freq' => 'daily', 'priority' => '0.9'],
                ['route' => 'projects.index', 'freq' => 'weekly', 'priority' => '0.8'],
                ['route' => 'agents.index', 'freq' => 'weekly', 'priority' => '0.7'],
                ['route' => 'articles.index', 'freq' => 'weekly', 'priority' => '0.7'],
                ['route' => 'pricing', 'freq' => 'monthly', 'priority' => '0.5'],
            ];

            foreach ($static as $item) {
                $urls[] = [
                    'loc' => route($item['route']),
                    'lastmod' => now()->format('Y-m-d'),
                    'changefreq' => $item['freq'],
                    'priority' => $item['priority'],
                ];
            }

            Property::query()
                ->whereNotNull('published_at')
                ->whereNull('sold_at')
                ->select(['slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($properties) use (&$urls): void {
                    foreach ($properties as $property) {
                        $urls[] = [
                            'loc' => route('properties.show', ['slug' => $property->slug]),
                            'lastmod' => optional($property->updated_at)->format('Y-m-d'),
                            'changefreq' => 'weekly',
                            'priority' => '0.8',
                        ];
                    }
                });

            Project::query()
                ->select(['slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($projects) use (&$urls): void {
                    foreach ($projects as $project) {
                        $urls[] = [
                            'loc' => route('projects.show', ['slug' => $project->slug]),
                            'lastmod' => optional($project->updated_at)->format('Y-m-d'),
                            'changefreq' => 'weekly',
                            'priority' => '0.7',
                        ];
                    }
                });

            Article::query()
                ->select(['slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($articles) use (&$urls): void {
                    foreach ($articles as $article) {
                        $urls[] = [
                            'loc' => route('articles.show', ['slug' => $article->slug]),
                            'lastmod' => optional($article->updated_at)->format('Y-m-d'),
                            'changefreq' => 'weekly',
                            'priority' => '0.6',
                        ];
                    }
                });

            Auction::query()
                ->where('status', 'open')
                ->select(['id', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($auctions) use (&$urls): void {
                    foreach ($auctions as $auction) {
                        $urls[] = [
                            'loc' => route('auctions.show', ['auction' => $auction->id]),
                            'lastmod' => optional($auction->updated_at)->format('Y-m-d'),
                            'changefreq' => 'daily',
                            'priority' => '0.6',
                        ];
                    }
                });

            Service::query()
                ->select(['id', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($services) use (&$urls): void {
                    foreach ($services as $service) {
                        $urls[] = [
                            'loc' => route('services.show', ['service' => $service->id]),
                            'lastmod' => optional($service->updated_at)->format('Y-m-d'),
                            'changefreq' => 'weekly',
                            'priority' => '0.5',
                        ];
                    }
                });

            AgentProfile::query()
                ->whereNotNull('verified_at')
                ->select(['id', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($agents) use (&$urls): void {
                    foreach ($agents as $agent) {
                        $urls[] = [
                            'loc' => route('agents.show', ['id' => $agent->id]),
                            'lastmod' => optional($agent->updated_at)->format('Y-m-d'),
                            'changefreq' => 'weekly',
                            'priority' => '0.5',
                        ];
                    }
                });

            $listings = ['jual', 'sewa'];
            $types = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];
            $cities = ['depok', 'jakarta-selatan', 'bekasi', 'surabaya', 'bandung'];

            foreach ($listings as $listing) {
                foreach ($types as $type) {
                    foreach ($cities as $citySlug) {
                        $cityName = ucwords(str_replace('-', ' ', $citySlug));

                        $exists = Property::query()
                            ->whereNotNull('published_at')
                            ->whereNull('sold_at')
                            ->where('type', $type)
                            ->where('listing_status', $listing)
                            ->where('city', 'like', '%'.$cityName.'%')
                            ->exists();

                        if ($exists) {
                            $urls[] = [
                                'loc' => route('seo.landing', [
                                    'listing' => $listing,
                                    'type' => $type,
                                    'city' => $citySlug,
                                ]),
                                'lastmod' => now()->format('Y-m-d'),
                                'changefreq' => 'weekly',
                                'priority' => '0.7',
                            ];
                        }
                    }
                }
            }

            return $urls;
        });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
