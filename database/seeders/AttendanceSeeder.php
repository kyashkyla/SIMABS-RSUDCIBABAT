<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AttendanceSeeder extends Seeder
{
    /**
     * Membuat beberapa pegawai contoh beserta riwayat absensi
     * dan pengajuan absensi alternatif, agar dashboard/riwayat/
     * laporan/persetujuan admin bisa langsung diuji coba.
     */
    public function run(): void
    {
        $sample = [
            ['name' => 'Ahmad Fauzi', 'department' => 'Radiologi', 'position' => 'Radiografer', 'shift' => 'Pagi'],
            ['name' => 'Maya Kusuma', 'department' => 'IGD', 'position' => 'Perawat', 'shift' => 'Pagi'],
            ['name' => 'Rizky Saputra', 'department' => 'Farmasi', 'position' => 'Apoteker', 'shift' => 'Siang'],
            ['name' => 'Siti Aisyah', 'department' => 'Rawat Inap', 'position' => 'Perawat', 'shift' => 'Malam'],
            ['name' => 'Budi Santoso', 'department' => 'Laboratorium', 'position' => 'Analis Lab', 'shift' => 'Pagi'],
            ['name' => 'Deni Kurniawan', 'department' => 'Administrasi', 'position' => 'Staff Admin', 'shift' => 'Siang'],
        ];

        $employees = collect();

        foreach ($sample as $i => $data) {
            $username = Str::slug($data['name'], '') ?: 'pegawai'.$i;

            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name' => $data['name'],
                    'email' => $username.'@rscibabat.com',
                    'password' => Hash::make('pegawai123'),
                    'role' => 'pegawai',
                ]
            );

            $employee = Employee::firstOrCreate(
                ['nip' => '19870000'.str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'nik' => '32040000'.str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                    'email' => $username.'@rscibabat.com',
                    'phone' => '08123456'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'gender' => $i % 2 === 0 ? 'Laki-laki' : 'Perempuan',
                    'position' => $data['position'],
                    'department' => $data['department'],
                    'status' => 'Aktif',
                    'start_date' => now()->subYears(2),
                    'shift' => $data['shift'],
                    'face_id_registered' => true,
                ]
            );

            $employees->push($employee);
        }

        // Buat riwayat absensi 7 hari terakhir
        for ($d = 6; $d >= 0; $d--) {
            $date = now()->subDays($d)->toDateString();

            foreach ($employees as $i => $employee) {
                // Variasi status agar dashboard/laporan punya data yang beragam
                $roll = ($d + $i) % 5;

                if ($roll === 4) {
                    // Tidak hadir -> tidak dibuatkan baris absensi sama sekali,
                    // supaya konsisten dengan cara admin menghitung "tidak hadir"
                    // (pegawai yang belum absen hari itu).
                    continue;
                }

                $isLate = $roll === 3;

                Attendance::updateOrCreate(
                    ['employee_id' => $employee->id, 'attendance_date' => $date],
                    [
                        'status' => $isLate ? 'terlambat' : 'hadir',
                        'check_in_method' => 'face',
                        'check_in_at' => $date.' '.($isLate ? '08:15:00' : '07:0'.$i.':00'),
                        'check_out_at' => $date.' 15:00:00',
                    ]
                );
            }
        }

        // Contoh pengajuan absensi alternatif (KF-06/KF-07)
        AttendanceRequest::firstOrCreate(
            ['employee_id' => $employees[0]->id, 'reason' => 'Face ID tidak dapat digunakan karena kamera perangkat rusak'],
            ['status' => 'pending']
        );

        AttendanceRequest::firstOrCreate(
            ['employee_id' => $employees[1]->id, 'reason' => 'Kendala jaringan saat verifikasi wajah'],
            ['status' => 'pending']
        );
    }
}