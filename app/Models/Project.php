<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'developer_user_id',
        'name',
        'slug',
        'city',
        'price_from',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_from' => 'integer',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_user_id');
    }
}
