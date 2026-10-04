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
        // PENTING: cache HANYA menyimpan nilai mentah dari DB (atau null kalau
        // belum diset). Default TIDAK ikut disimpan ke cache, supaya pemanggil
        // lain yang memberi default berbeda untuk key yang sama tidak ikut
        // "terkunci" ke default milik pemanggil pertama yang mengisi cache.
        $value = Cache::rememberForever("setting:{$key}", function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        return $value ?? $default;
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