<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('employee');

        if (!$user->employee) {
            abort(404, 'Data pegawai tidak ditemukan.');
        }

        $employee = $user->employee;
        $today = Carbon::today();

        return Inertia::render('Auth/pegawai/Dashboard', [
            'user' => $user,
            'employee' => $employee,
            'todayAttendance' => $this->formatTodayAttendance($employee),
            'monthlySummary' => $this->buildMonthlySummary($employee, $today),
            'recentActivities' => $this->buildRecentActivities($employee),
        ]);
    }

    /**
     * Absensi hari ini pegawai yang login, buat kartu "Status Hari Ini" &
     * "Jam Masuk". Null kalau belum absen sama sekali hari ini.
     */
    private function formatTodayAttendance($employee): ?array
    {
        $attendance = $employee->todayAttendance();

        if (!$attendance) {
            return null;
        }

        return [
            'check_in_at' => $attendance->check_in_at?->format('H:i'),
            'status' => $attendance->status,
            'method' => $attendance->check_in_method,
        ];
    }

    /**
     * Ringkasan kehadiran bulan berjalan (kartu "Ringkasan Bulan" &
     * "Kehadiran Bulan Ini"). Otomatis reset tiap awal bulan karena
     * query-nya selalu dibatasi ke bulan & tahun HARI INI, jadi begitu
     * tanggal 1 datang, otomatis dihitung dari 0 lagi.
     */
    private function buildMonthlySummary($employee, Carbon $today): array
    {
        $monthStart = $today->copy()->startOfMonth();

        $attendances = $employee->attendances()
            ->whereYear('attendance_date', $today->year)
            ->whereMonth('attendance_date', $today->month)
            ->get();

        $hadir = $attendances->where('status', 'hadir')->count();
        $terlambat = $attendances->where('status', 'terlambat')->count();
        $izin = $attendances->where('status', 'alternatif')->count();

        // Jumlah hari yang sudah lewat di bulan ini, sampai hari ini (dipakai
        // sebagai penyebut supaya "Tidak Hadir" & persentase masuk akal).
        $hariBerjalan = $today->day;
        $tidakHadir = max($hariBerjalan - ($hadir + $terlambat + $izin), 0);

        $tingkatKehadiran = $hariBerjalan > 0
            ? round((($hadir + $terlambat) / $hariBerjalan) * 100, 1)
            : 0;

        return [
            'year' => $today->year,
            'month' => $today->month,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'tidak_hadir' => $tidakHadir,
            'hari_berjalan' => $hariBerjalan,
            'tingkat_kehadiran' => $tingkatKehadiran,
        ];
    }

    /**
     * 5 aktivitas absensi terakhir buat kartu "Aktivitas Terkini".
     */
    private function buildRecentActivities($employee)
    {
        return $employee->attendances()
            ->whereNotNull('check_in_at')
            ->orderByDesc('attendance_date')
            ->limit(5)
            ->get()
            ->map(fn ($attendance) => [
                'attendance_date' => $attendance->attendance_date->toDateString(),
                'check_in_at' => $attendance->check_in_at?->format('H:i'),
                'method' => $attendance->check_in_method,
                'status' => $attendance->status,
            ])
            ->values();
    }
}