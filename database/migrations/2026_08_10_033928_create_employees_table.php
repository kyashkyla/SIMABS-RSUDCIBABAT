<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Data identitas
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('nik')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan']);

            // Data kepegawaian
            $table->string('position');
            $table->string('department');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->date('start_date')->nullable();

            // Shift
            $table->enum('shift', ['Pagi', 'Siang', 'Malam']);

            // Face ID
            $table->boolean('face_id_registered')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};