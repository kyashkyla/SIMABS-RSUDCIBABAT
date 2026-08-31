<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Menghitung jam kerja & jendela absensi tiap shift.
 *
 * Admin cukup atur JAM MULAI tiap shift di config/attendance.php.
 * Jam selesai, jendela absen masuk, jendela absen pulang, dan batas
 * terlambat semuanya diturunkan otomatis dari sini (1 shift = 8 jam kerja,
 * atau sesuai 'shift_duration_hours').
 */
class ShiftSchedule
{
    /**
     * Ambil definisi lengkap SEMUA shift yang terdaftar di config.
     * Dipakai untuk menampilkan pilihan shift + jamnya di form
     * tambah/edit pegawai.
     *
     * @return array<string, array>
     */
    public static function all(): array
    {
        $shifts = [];

        foreach (array_keys(config('attendance.shift_start', [])) as $name) {
            $shifts[$name] = self::definition($name);
        }

        return $shifts;
    }

    /**
     * Ambil definisi satu shift: jam mulai, jam selesai, apakah shift ini
     * melewati tengah malam, jendela absen masuk, jendela absen pulang,
     * dan batas jam dianggap terlambat.
     *
     * Semua jam di sini berupa string "H:i" (jam-menit saja, tanpa tanggal),
     * karena shift adalah pola jam yang berulang tiap hari.
     */
    public static function definition(?string $shiftName): array
    {
        $start = config("attendance.shift_start.{$shiftName}");

        // Shift tidak dikenali / kosong -> fallback aman ke jam kantor umum
        // supaya sistem tidak error, tapi ini seharusnya tidak pernah terjadi
        // karena kolom shift wajib diisi salah satu dari Pagi/Siang/Malam.
        if (!$start) {
            $start = '07:00';
        }

        $durationHours = (int) config('attendance.shift_duration_hours', 8);
        $checkInMinutes = (int) config('attendance.check_in_window_minutes', 30);
        $checkOutMinutes = (int) config('attendance.check_out_window_minutes', 30);
        $lateAfterMinutes = (int) config('attendance.late_after_minutes', 15);

        $startTime = Carbon::parse($start);
        $endTime = $startTime->copy()->addHours($durationHours);

        // Shift dianggap "melewati tengah malam" kalau jam selesainya lebih
        // kecil/sama dari jam mulainya (mis. mulai 23:00 -> selesai 07:00).
        $overnight = $endTime->format('H:i') <= $startTime->format('H:i');

        return [
            'name' => $shiftName,
            'start' => $startTime->format('H:i'),
            'end' => $endTime->format('H:i'),
            'duration_hours' => $durationHours,
            'overnight' => $overnight,
            'check_in' => [
                'start' => $startTime->format('H:i'),
                'end' => $startTime->copy()->addMinutes($checkInMinutes)->format('H:i'),
            ],
            'check_out' => [
                'start' => $endTime->format('H:i'),
                'end' => $endTime->copy()->addMinutes($checkOutMinutes)->format('H:i'),
            ],
            'late_after' => $startTime->copy()->addMinutes($lateAfterMinutes)->format('H:i'),
        ];
    }

    /**
     * Jendela absen MASUK untuk sebuah shift pada tanggal tertentu, dalam
     * bentuk objek Carbon lengkap (tanggal + jam), siap dibandingkan
     * dengan waktu sekarang.
     */
    public static function checkInWindow(?string $shiftName, string $date): array
    {
        $shift = self::definition($shiftName);

        return [
            'start' => Carbon::parse("{$date} {$shift['check_in']['start']}"),
            'end' => Carbon::parse("{$date} {$shift['check_in']['end']}"),
        ];
    }

    /**
     * Jendela absen PULANG untuk shift yang absen masuknya tercatat pada
     * $attendanceDate. Kalau shiftnya melewati tengah malam (mis. Malam),
     * jendela pulangnya otomatis jatuh pada tanggal BERIKUTNYA.
     */
    public static function checkOutWindow(?string $shiftName, string $attendanceDate): array
    {
        $shift = self::definition($shiftName);

        $endDate = $shift['overnight']
            ? Carbon::parse($attendanceDate)->addDay()->toDateString()
            : $attendanceDate;

        return [
            'start' => Carbon::parse("{$endDate} {$shift['check_out']['start']}"),
            'end' => Carbon::parse("{$endDate} {$shift['check_out']['end']}"),
        ];
    }

    /**
     * Batas jam "terlambat" untuk shift, pada tanggal absen masuk tertentu.
     */
    public static function lateAfter(?string $shiftName, string $date): Carbon
    {
        $shift = self::definition($shiftName);

        return Carbon::parse("{$date} {$shift['late_after']}");
    }
}