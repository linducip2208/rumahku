<?php

namespace App\Models;

use Database\Factories\KprApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KprApplication extends Model
{
    /** @use HasFactory<KprApplicationFactory> */
    use HasFactory;

    public const TENORS = [12, 24, 36, 60, 120, 180, 240];

    public const BANKS = ['mandiri', 'bca', 'bri', 'bni', 'syariah'];

    public const STATUSES = ['pending', 'contacted', 'approved', 'rejected'];

    protected $fillable = [
        'user_id',
        'property_id',
        'name',
        'phone',
        'email',
        'dp_amount',
        'tenor_months',
        'bank_preference',
        'monthly_income',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dp_amount' => 'integer',
            'tenor_months' => 'integer',
            'monthly_income' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
