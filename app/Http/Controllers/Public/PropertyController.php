<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Laravel\Scout\Builder as ScoutBuilder;

class PropertyController extends Controller
{
    private const ALLOWED_TYPES = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];

    private const ALLOWED_STATUSES = ['jual', 'sewa'];

    public function index(Request $request): View
    {
        if ($request->filled('q')) {
            $scoutProperties = $this->searchViaScout($request);

            if ($scoutProperties !== null) {
                return view('pages.index', [
                    'properties' => $scoutProperties,
                    'filters' => $request->only(['q', 'type', 'status', 'city', 'min_price', 'max_price', 'bedrooms']),
                ]);
            }
        }

        $query = Property::query()
            ->with('images')
            ->whereNotNull('published_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('district', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $type = strtolower($request->string('type')->toString());

            if (in_array($type, self::ALLOWED_TYPES, true)) {
                $query->where('type', $type);
            }
        }

        if ($request->filled('status')) {
            $status = $this->normalizeStatus($request->string('status')->toString());

            if (in_array($status, self::ALLOWED_STATUSES, true)) {
                $query->where('listing_status', $status);
            }
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->string('city')->toString().'%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->input('bedrooms'));
        }

        $properties = $query->latest()->paginate(12)->withQueryString();

        return view('pages.index', [
            'properties' => $properties,
            'filters' => $request->only(['q', 'type', 'status', 'city', 'min_price', 'max_price', 'bedrooms']),
        ]);
    }

    public function show(string $slug): View
    {
        $property = Property::query()
            ->with(['images', 'user'])
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        $property->increment('views');

        $related = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->latest()
            ->take(4)
            ->get();

        return view('pages.show', [
            'property' => $property,
            'related' => $related,
        ]);
    }

    private function normalizeStatus(string $status): string
    {
        return match (strtolower($status)) {
            'dijual', 'jual' => 'jual',
            'disewa', 'disewakan', 'sewa' => 'sewa',
            default => $status,
        };
    }

    /**
     * Pencarian via Laravel Scout. Mengembalikan null bila Scout gagal
     * (mis. driver/index bermasalah) agar pemanggil fallback ke query
     * manual dan tidak 500.
     *
     * @return LengthAwarePaginator<int, Property>|null
     */
    private function searchViaScout(Request $request): mixed
    {
        try {
            /** @var ScoutBuilder<Property> $scout */
            $scout = Property::search($request->string('q')->toString());

            return $scout
                ->query(fn (EloquentBuilder $query) => $this->applyListingFilters($query, $request))
                ->paginate(12)
                ->withQueryString();
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function applyListingFilters(EloquentBuilder $query, Request $request): void
    {
        $query->with('images')->whereNotNull('published_at');

        if ($request->filled('type')) {
            $type = strtolower($request->string('type')->toString());

            if (in_array($type, self::ALLOWED_TYPES, true)) {
                $query->where('type', $type);
            }
        }

        if ($request->filled('status')) {
            $status = $this->normalizeStatus($request->string('status')->toString());

            if (in_array($status, self::ALLOWED_STATUSES, true)) {
                $query->where('listing_status', $status);
            }
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->string('city')->toString().'%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->input('bedrooms'));
        }

        $query->orderByDesc('featured')->latest();
    }
}
