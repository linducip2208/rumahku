<?php

namespace App\Models;

use App\Concerns\LogsActivity;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'property_id',
        'agent_user_id',
        'name',
        'phone',
        'message',
        'source',
        'status',
        'follow_up_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function isOpen(): bool
    {
        return ! in_array($this->status, ['closing', 'gagal'], true);
    }
}
