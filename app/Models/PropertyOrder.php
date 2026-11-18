<?php

namespace App\Models;

use App\Concerns\LogsActivity;
use Database\Factories\PropertyOrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Featured order: pembelian paket unggulan untuk sebuah properti.
 */
class PropertyOrder extends Model
{
    /** @use HasFactory<PropertyOrderFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'package_slug',
        'amount',
        'status',
        'paid_at',
        'expires_at',
        'payment_reference',
        'payment_driver',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_slug', 'slug');
    }

    /**
     * Order aktif: sudah dibayar dan belum kedaluwarsa (atau tanpa batas waktu).
     *
     * @param  Builder<PropertyOrder>  $query
     * @return Builder<PropertyOrder>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'paid')
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function markAsPaid(): bool
    {
        if ($this->status === 'paid') {
            return true;
        }

        $package = Package::query()->where('slug', $this->package_slug)->first();

        $durationDays = $package?->duration_days ?? 30;

        $updated = $this->forceFill([
            'status' => 'paid',
            'paid_at' => now(),
            'expires_at' => now()->addDays($durationDays),
        ])->save();

        if ($package?->featured_included) {
            $this->property()->update(['featured' => true]);
        }

        return $updated;
    }
}
