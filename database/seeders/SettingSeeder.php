<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Nilai default pengaturan sistem, terutama lokasi & radius absensi
     * yang dipersyaratkan pada KNF-06 (Validasi Lokasi Real-Time).
     */
    public function run(): void
    {
        $defaults = [
            // Lokasi & radius absensi (KF-04 / KNF-06)
            ['key' => 'office_latitude', 'value' => '-6.826000', 'label' => 'Latitude Lokasi RSUD', 'group' => 'lokasi'],
            ['key' => 'office_longitude', 'value' => '107.522000', 'label' => 'Longitude Lokasi RSUD', 'group' => 'lokasi'],
            ['key' => 'office_radius', 'value' => '100', 'label' => 'Radius Absensi (meter)', 'group' => 'lokasi'],
            ['key' => 'office_address', 'value' => 'RSUD Cibabat, Kota Cimahi, Jawa Barat', 'label' => 'Alamat Lokasi', 'group' => 'lokasi'],

            // Jam kerja & toleransi (mendukung KF-09 Pencatatan Kehadiran)
            ['key' => 'work_start', 'value' => '07:00', 'label' => 'Jam Masuk', 'group' => 'jam_kerja'],
            ['key' => 'work_end', 'value' => '15:00', 'label' => 'Jam Pulang', 'group' => 'jam_kerja'],
            ['key' => 'late_tolerance_minutes', 'value' => '15', 'label' => 'Toleransi Keterlambatan (menit)', 'group' => 'jam_kerja'],

            // Notifikasi sistem (KF-12)
            ['key' => 'notify_admin_new_request', 'value' => '1', 'label' => 'Notifikasi pengajuan baru ke admin', 'group' => 'notifikasi'],
            ['key' => 'notify_employee_decision', 'value' => '1', 'label' => 'Notifikasi keputusan ke pegawai', 'group' => 'notifikasi'],
        ];

        foreach ($defaults as $item) {
            Setting::firstOrCreate(['key' => $item['key']], $item);
        }
    }
}
