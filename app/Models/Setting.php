<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key-value settings yang diisi via admin (Filament), bukan .env.
 *
 * Semua kredensial payment gateway WAJIB lewat sini.
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'value',
    ];

    public static function cacheKey(string $key): string
    {
        return 'settings.'.$key;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (Cache::has(static::cacheKey($key))) {
            return Cache::get(static::cacheKey($key));
        }

        $value = static::query()->where('key', $key)->value('value');

        if ($value === null) {
            return $default;
        }

        Cache::forever(static::cacheKey($key), $value);

        return $value;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        if ($value === null) {
            return $default;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function set(string $key, mixed $value, ?string $group = null): static
    {
        $setting = static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value === null ? null : (string) $value,
                'group' => $group,
            ]
        );

        Cache::forever(static::cacheKey($key), (string) $value);

        return $setting;
    }

    public static function forget(string $key): void
    {
        static::query()->where('key', $key)->delete();

        Cache::forget(static::cacheKey($key));
    }

    /**
     * @return array<string, ?string>
     */
    public static function getMany(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $value = static::get($key);
            $result[$key] = $value !== null ? (string) $value : null;
        }

        return $result;
    }
}
