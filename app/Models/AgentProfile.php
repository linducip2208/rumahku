<?php

namespace App\Models;

use Database\Factories\AgentProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentProfile extends Model
{
    /** @use HasFactory<AgentProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agency',
        'phone',
        'wa',
        'photo',
        'verified_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
