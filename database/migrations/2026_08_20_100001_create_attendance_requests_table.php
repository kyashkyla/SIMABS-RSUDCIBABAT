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
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            // KF-06: alasan kegagalan verifikasi wajah / pengajuan absensi alternatif
            $table->text('reason');

            // Bukti pendukung (path file di storage)
            $table->string('selfie_photo')->nullable();
            $table->string('id_card_photo')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // Jenis akses yang diberikan bila disetujui (KF-07)
            $table->enum('approval_type', ['sementara', 'permanen'])->nullable();

            $table->text('admin_note')->nullable();

            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('decided_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_requests');
    }
};
