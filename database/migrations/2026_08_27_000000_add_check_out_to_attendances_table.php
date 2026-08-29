<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('check_out_at')->nullable()->after('check_in_photo');
            $table->decimal('check_out_lat', 10, 7)->nullable()->after('check_out_at');
            $table->decimal('check_out_lng', 10, 7)->nullable()->after('check_out_lat');
            $table->unsignedInteger('check_out_distance_meters')->nullable()->after('check_out_lng');
            $table->enum('check_out_method', ['face', 'otp', 'alternative'])->nullable()->after('check_out_distance_meters');
            $table->string('check_out_photo')->nullable()->after('check_out_method');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_out_at',
                'check_out_lat',
                'check_out_lng',
                'check_out_distance_meters',
                'check_out_method',
                'check_out_photo',
            ]);
        });
    }
};