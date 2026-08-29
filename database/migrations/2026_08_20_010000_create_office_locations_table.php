<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // Titik pusat lokasi kantor (WGS84 / GPS biasa)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Radius area yang diizinkan untuk absen, dalam meter
            $table->unsignedInteger('radius_meters')->default(100);

            // Hanya 1 lokasi yang boleh aktif dalam satu waktu.
            // Diatur oleh admin lewat halaman pengaturan lokasi.
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_locations');
    }
};