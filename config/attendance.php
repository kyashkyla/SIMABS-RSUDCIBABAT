<?php

// config/attendance.php

return [
    /*
    |--------------------------------------------------------------------------
    | Jendela Waktu Absensi
    |--------------------------------------------------------------------------
    |
    | Pegawai hanya boleh absen masuk / pulang selama rentang jam ini.
    | Di luar rentang ini, permintaan absensi otomatis ditolak oleh server
    | (bukan cuma disembunyikan di UI). Sesuaikan angkanya lewat .env,
    | atau langsung di sini kalau tidak mau lewat .env.
    |
    | Contoh di bawah: masing-masing jendela lebarnya 45 menit.
    | Ganti sesuai jam kerja RSUD Cibabat yang sebenarnya.
    |
    */

    'check_in' => [
        'start' => env('ATTENDANCE_CHECKIN_START', '07:00'),
        'end'   => env('ATTENDANCE_CHECKIN_END', '07:45'),
    ],

    // Absen masuk yang masuk dalam jendela tapi lewat jam ini tetap
    // diterima, cuma statusnya "terlambat" bukan "hadir".
    'late_after' => env('ATTENDANCE_LATE_AFTER', '07:15'),

    'check_out' => [
        'start' => env('ATTENDANCE_CHECKOUT_START', '15:15'),
        'end'   => env('ATTENDANCE_CHECKOUT_END', '16:00'),
    ],
];