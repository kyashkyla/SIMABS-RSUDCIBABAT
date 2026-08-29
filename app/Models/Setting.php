<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'group'];

    /**
     * Ambil nilai satu setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Simpan/perbarui satu setting berdasarkan key.
     */
    public static function set(string $key, mixed $value, ?string $label = null, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'label' => $label,
                'group' => $group,
            ], fn ($v) => $v !== null)
        );

        Cache::forget("setting:{$key}");
    }

    /**
     * Ambil seluruh setting dalam satu grup sebagai array asosiatif [key => value].
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }
}
