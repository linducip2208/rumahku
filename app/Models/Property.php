<?php

namespace App\Models;

use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, Searchable;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'type',
        'listing_status',
        'price',
        'bedrooms',
        'bathrooms',
        'land_area',
        'building_area',
        'certificate',
        'province',
        'city',
        'district',
        'address',
        'lat',
        'lng',
        'description',
        'featured',
        'verified_at',
        'views',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'land_area' => 'float',
            'building_area' => 'float',
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'featured' => 'boolean',
            'verified_at' => 'datetime',
            'published_at' => 'datetime',
            'views' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Data yang diindeks Scout. Engine database memakai kolom ini untuk
     * pencocokan LIKE; engine Meilisearch memakai field yang sama tanpa
     * perubahan kode — cukup ganti SCOUT_DRIVER + config.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'city' => $this->city,
            'district' => $this->district,
            'province' => $this->province,
            'type' => $this->type,
            'listing_status' => $this->listing_status,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->published_at !== null;
    }
}
