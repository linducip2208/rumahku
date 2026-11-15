<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureIdea extends Model
{
    /** @use HasFactory<FeatureIdea> */
    use HasFactory;

    protected $fillable = [
        'feature_key',
        'slot',
        'title',
        'description',
        'audience',
        'need',
        'mechanism',
        'impact',
        'effort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'slot' => 'integer',
        ];
    }
}
