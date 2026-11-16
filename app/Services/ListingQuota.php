<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\User;

class ListingQuota
{
    public const FREE_LIMIT = 5;

    /**
     * Kuota efektif user: max max_listings dari paket pada order
     * berbayar & belum kedaluwarsa. null = unlimited.
     *
     * @return ?int null berarti tanpa batas
     */
    public static function limitFor(User $user): ?int
    {
        $slugs = PropertyOrder::query()
            ->where('user_id', $user->getKey())
            ->active()
            ->pluck('package_slug')
            ->unique()
            ->values();

        if ($slugs->isEmpty()) {
            return self::FREE_LIMIT;
        }

        $limits = Package::query()
            ->whereIn('slug', $slugs)
            ->pluck('max_listings');

        if ($limits->contains(fn (?int $v): bool => $v === null)) {
            return null;
        }

        $max = $limits->filter(fn (?int $v): bool => $v !== null)->max();

        return $max === null ? self::FREE_LIMIT : (int) $max;
    }

    /**
     * Jumlah listing aktif milik user (published & belum terjual).
     */
    public static function usageFor(User $user): int
    {
        return Property::query()
            ->where('user_id', $user->getKey())
            ->active()
            ->count();
    }

    /**
     * True bila user masih boleh membuat listing baru.
     */
    public static function canCreate(User $user): bool
    {
        $limit = self::limitFor($user);

        if ($limit === null) {
            return true;
        }

        return self::usageFor($user) < $limit;
    }
}
