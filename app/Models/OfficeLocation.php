<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Jarak antara titik (lat, lng) dengan lokasi kantor ini, dalam meter.
     * Pakai rumus Haversine (permukaan bumi dianggap bulat sempurna,
     * cukup akurat untuk radius absensi puluhan-ratusan meter).
     */
    public function distanceInMetersFrom(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // meter

        $latDelta = deg2rad($lat - $this->latitude);
        $lngDelta = deg2rad($lng - $this->longitude);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($lngDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isWithinRadius(float $lat, float $lng): bool
    {
        return $this->distanceInMetersFrom($lat, $lng) <= $this->radius_meters;
    }
}