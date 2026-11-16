<?php

namespace App\Models;

use App\Concerns\LogsActivity;
use Database\Factories\AuctionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Auction extends Model
{
    /** @use HasFactory<AuctionFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'property_id',
        'starts_at',
        'ends_at',
        'start_price',
        'current_price',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'start_price' => 'integer',
            'current_price' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class)->latest();
    }

    /**
     * Minimum increment: 1% dari harga saat ini, minimal Rp 1 juta.
     */
    public function minimumNextBid(): int
    {
        $base = $this->current_price ?? $this->start_price;

        return $base + (int) max(1000000, round($base * 0.01));
    }

    /**
     * @throws ValidationException
     */
    public function placeBid(User $user, int $amount): AuctionBid
    {
        $bid = DB::transaction(function () use ($user, $amount): AuctionBid {
            /** @var Auction $auction */
            $auction = self::query()->whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if ($auction->status !== 'open') {
                throw ValidationException::withMessages([
                    'amount' => 'Lelang ini tidak sedang dibuka.',
                ]);
            }

            $now = now();

            if (($auction->starts_at && $now->lt($auction->starts_at))
                || ($auction->ends_at && $now->gt($auction->ends_at))) {
                throw ValidationException::withMessages([
                    'amount' => 'Lelang ini sedang tidak berlangsung.',
                ]);
            }

            $minimum = $auction->minimumNextBid();

            if ($amount < $minimum) {
                throw ValidationException::withMessages([
                    'amount' => 'Tawaran minimal Rp '.number_format($minimum, 0, ',', '.').'.',
                ]);
            }

            $created = $auction->bids()->create([
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            $auction->update(['current_price' => $amount]);

            return $created;
        });

        $this->refresh();

        return $bid;
    }
}
