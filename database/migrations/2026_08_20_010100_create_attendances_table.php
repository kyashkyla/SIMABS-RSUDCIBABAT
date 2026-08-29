<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            // Data check-in
            $table->dateTime('check_in_at')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->unsignedInteger('check_in_distance_meters')->nullable();
            $table->enum('check_in_method', ['face', 'otp', 'alternative'])->nullable();
            $table->string('check_in_photo')->nullable();

            $table->enum('status', ['hadir', 'terlambat', 'alternatif'])->default('hadir');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu pegawai hanya punya satu baris absensi per tanggal
            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};