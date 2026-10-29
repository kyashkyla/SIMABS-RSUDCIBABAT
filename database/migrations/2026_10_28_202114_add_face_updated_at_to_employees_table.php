<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Kapan foto wajah (acuan Face ID) terakhir didaftarkan / diganti.
            $table->timestamp('face_updated_at')->nullable()->after('photo');
        });

        // Data lama: kita tidak tahu kapan fotonya diganti, jadi pakai
        // tanggal akun pegawai dibuat sebagai perkiraan terbaik.
        DB::table('employees')
            ->whereNotNull('photo')
            ->update(['face_updated_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('face_updated_at');
        });
    }
};