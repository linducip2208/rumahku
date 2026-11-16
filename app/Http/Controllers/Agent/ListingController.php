<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $properties = Property::query()
            ->where('user_id', $request->user()->id)
            ->when($status === 'aktif', fn ($query) => $query->whereNull('sold_at'))
            ->when($status === 'terjual', fn ($query) => $query->whereNotNull('sold_at'))
            ->with('images')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('agent.listings.index', compact('properties', 'status'));
    }

    public function create(): View
    {
        return view('agent.listings.form', ['property' => new Property]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $property = Property::query()->create([
            ...$this->payloadAttributes($validated),
            'user_id' => $request->user()->id,
            'slug' => $this->uniqueSlug($validated['title']),
            'published_at' => now(),
        ]);

        $this->storeImages($request, $property);

        return redirect()->route('agent.listings.index')->with('success', 'Listing berhasil dibuat.');
    }

    public function edit(Request $request, Property $listing): RedirectResponse|View
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        $listing->load('images');

        return view('agent.listings.form', ['property' => $listing]);
    }

    public function update(Request $request, Property $listing): RedirectResponse
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $this->validatePayload($request);

        $attributes = $this->payloadAttributes($validated);

        if ($listing->title !== $validated['title']) {
            $attributes['slug'] = $this->uniqueSlug($validated['title'], $listing->id);
        }

        $listing->update($attributes);

        $this->removeImages($request, $listing);
        $this->storeImages($request, $listing);

        return redirect()->route('agent.listings.index')->with('success', 'Listing berhasil diperbarui.');
    }

    public function destroy(Request $request, Property $listing): RedirectResponse
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        foreach ($listing->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $listing->delete();

        return redirect()->route('agent.listings.index')->with('success', 'Listing berhasil dihapus.');
    }

    public function sold(Request $request, Property $listing): RedirectResponse
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($listing->sold_at === null) {
            $listing->update(['sold_at' => now()]);
        }

        return redirect()->route('agent.listings.index')->with('success', 'Listing ditandai terjual.');
    }

    public function relist(Request $request, Property $listing): RedirectResponse
    {
        if ($listing->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($listing->sold_at !== null) {
            $listing->update(['sold_at' => null]);
        }

        return redirect()->route('agent.listings.index')->with('success', 'Listing diaktifkan kembali.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:rumah,apartemen,tanah,ruko,villa'],
            'listing_status' => ['required', 'string', 'in:jual,sewa'],
            'price' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'land_area' => ['nullable', 'numeric', 'min:0'],
            'building_area' => ['nullable', 'numeric', 'min:0'],
            'certificate' => ['nullable', 'string', 'max:50'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
        ]);

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payloadAttributes(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'listing_status' => $validated['listing_status'],
            'price' => $validated['price'],
            'bedrooms' => $validated['bedrooms'] ?? null,
            'bathrooms' => $validated['bathrooms'] ?? null,
            'land_area' => $validated['land_area'] ?? null,
            'building_area' => $validated['building_area'] ?? null,
            'certificate' => $validated['certificate'] ?? null,
            'province' => $validated['province'],
            'city' => $validated['city'],
            'district' => $validated['district'] ?? null,
            'address' => $validated['address'],
            'description' => $validated['description'],
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'properti';
        $slug = $base;
        $counter = 1;

        while (
            Property::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $counter++;
            $slug = $base.'-'.$counter;
        }

        return $slug;
    }

    private function storeImages(Request $request, Property $property): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sort = (int) ($property->images()->max('sort') ?? -1) + 1;

        foreach ((array) $request->file('images') as $file) {
            if ($file === null || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('properties', 'public');

            PropertyImage::query()->create([
                'property_id' => $property->id,
                'path' => $path,
                'sort' => $sort++,
            ]);
        }
    }

    private function removeImages(Request $request, Property $property): void
    {
        $ids = array_map('intval', (array) $request->input('remove_images', []));

        if ($ids === []) {
            return;
        }

        $images = PropertyImage::query()
            ->where('property_id', $property->id)
            ->whereIn('id', $ids)
            ->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
