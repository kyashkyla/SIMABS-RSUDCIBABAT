<?php

namespace Database\Seeders;

use App\Models\OfficeLocation;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    /**
     * Titik koordinat resmi RSUD Cibabat, diambil langsung dari
     * konfigurasi aplikasi eksisting (halaman "Konfigurasi Aplikasi").
     * Ada 2 titik karena RSUD Cibabat punya lebih dari satu area/gerbang
     * yang masing-masing dianggap valid untuk absensi.
     */
    public function run(): void
    {
        OfficeLocation::updateOrCreate(
            ['name' => 'RSUD Cibabat - Titik 1'],
            [
                'latitude' => -6.8791146,
                'longitude' => 107.5509875,
                'radius_meters' => 60,
                'is_active' => true,
            ]
        );

        OfficeLocation::updateOrCreate(
            ['name' => 'RSUD Cibabat - Titik 2'],
            [
                'latitude' => -6.8783719,
                'longitude' => 107.5515775,
                'radius_meters' => 60,
                'is_active' => true,
            ]
        );
    }
}