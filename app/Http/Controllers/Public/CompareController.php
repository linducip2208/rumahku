<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\RecommendationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request, RecommendationService $recommender): View
    {
        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn (string $v): int => (int) trim($v))
            ->filter(fn (int $v): bool => $v > 0)
            ->unique()
            ->take(4)
            ->values();

        if ($ids->isEmpty()) {
            $ids = collect(session('compare_ids', []))
                ->map(fn ($v): int => (int) $v)
                ->filter(fn (int $v): bool => $v > 0)
                ->take(4)
                ->values();
        }

        $properties = $ids->isNotEmpty()
            ? Property::query()->with('images')->whereIn('id', $ids->all())->get()->sortBy(fn (Property $p): int => array_search($p->id, $ids->all()))
            : collect();

        $recommendations = $properties->isNotEmpty()
            ? $recommender->related($properties->first(), 4)
            : collect();

        return view('pages.compare', [
            'properties' => $properties,
            'recommendations' => $recommendations,
        ]);
    }

    public function add(Request $request, Property $property): RedirectResponse
    {
        $ids = collect(session('compare_ids', []))->map(fn ($v): int => (int) $v);
        if (! $ids->contains($property->id)) {
            $ids->push($property->id);
        }
        session(['compare_ids' => $ids->take(4)->values()->all()]);

        return redirect()->route('compare.index', ['ids' => implode(',', session('compare_ids'))]);
    }

    public function remove(Request $request, Property $property): RedirectResponse
    {
        $ids = collect(session('compare_ids', []))
            ->map(fn ($v): int => (int) $v)
            ->reject(fn (int $v): bool => $v === $property->id)
            ->values()
            ->all();
        session(['compare_ids' => $ids]);

        return redirect()->route('compare.index', $ids === [] ? [] : ['ids' => implode(',', $ids)]);
    }
}
