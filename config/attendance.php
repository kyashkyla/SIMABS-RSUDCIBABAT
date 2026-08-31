<?php

// config/attendance.php

return [
    /*
    |--------------------------------------------------------------------------
    | Jam Mulai Tiap Shift
    |--------------------------------------------------------------------------
    |
    | Cukup atur JAM MULAI kerja masing-masing shift di sini (atau lewat .env).
    | Jam SELESAI, jendela absen masuk, jendela absen pulang, dan batas
    | terlambat dihitung OTOMATIS oleh App\Support\ShiftSchedule berdasarkan
    | 'shift_duration_hours' di bawah (1 shift = 8 jam kerja).
    |
    | Contoh dengan pengaturan default (mulai jam 07:00, durasi 8 jam):
    | - Pagi  : 07:00 - 15:00 (absen masuk 07:00-07:30, absen pulang 15:00-15:30)
    | - Siang : 15:00 - 23:00 (absen masuk 15:00-15:30, absen pulang 23:00-23:30)
    | - Malam : 23:00 - 07:00 (absen masuk 23:00-23:30, absen pulang 07:00-07:30
    |           pada hari berikutnya, karena shift ini melewati tengah malam)
    |
    */

    'shift_start' => [
        'Pagi' => env('SHIFT_PAGI_START', '07:00'),
        'Siang' => env('SHIFT_SIANG_START', '15:00'),
        'Malam' => env('SHIFT_MALAM_START', '23:00'),
    ],

    // Lama kerja satu shift (jam). Jam selesai tiap shift = jam mulai + ini.
    'shift_duration_hours' => (int) env('SHIFT_DURATION_HOURS', 8),

    // Lebar jendela absen MASUK, dihitung mulai dari jam mulai shift.
    // Default 30 menit -> mis. shift Pagi (mulai 07:00): absen masuk hanya
    // bisa dilakukan jam 07:00 - 07:30.
    'check_in_window_minutes' => (int) env('ATTENDANCE_CHECKIN_WINDOW_MINUTES', 30),

    // Lebar jendela absen PULANG, dihitung mulai dari jam selesai shift.
    // Default 30 menit -> mis. shift Pagi (selesai 15:00): absen pulang hanya
    // bisa dilakukan jam 15:00 - 15:30.
    'check_out_window_minutes' => (int) env('ATTENDANCE_CHECKOUT_WINDOW_MINUTES', 30),

    // Absen masuk yang masuk dalam jendela tapi lewat sekian menit dari jam
    // mulai shift tetap diterima, cuma statusnya "terlambat" bukan "hadir".
    'late_after_minutes' => (int) env('ATTENDANCE_LATE_AFTER_MINUTES', 15),
];