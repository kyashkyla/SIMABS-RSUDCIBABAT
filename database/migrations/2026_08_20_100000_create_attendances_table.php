<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('date');

            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();

            // Metode absensi yang digunakan
            $table->enum('method', ['face_id', 'otp'])->nullable();

            // Status kehadiran pada tanggal tsb
            $table->enum('status', ['hadir', 'terlambat', 'tidak_hadir', 'alternatif'])
                ->default('tidak_hadir');

            // Data lokasi saat absen (Geolocation - KF-04)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu pegawai hanya punya satu baris absensi per tanggal
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
