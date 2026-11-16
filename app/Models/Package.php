<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_days',
        'max_photos',
        'max_listings',
        'featured_included',
        'badge',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'duration_days' => 'integer',
            'max_photos' => 'integer',
            'max_listings' => 'integer',
            'featured_included' => 'boolean',
        ];
    }
}
