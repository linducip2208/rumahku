<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'developer_user_id',
        'name',
        'slug',
        'city',
        'price_from',
        'description',
        'facilities',
        'unit_types',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_from' => 'integer',
            'facilities' => 'array',
            'unit_types' => 'array',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_user_id');
    }
}
