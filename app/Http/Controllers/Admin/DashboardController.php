<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Dashboard utama admin: statistik hari ini, grafik mingguan,
     * ringkasan status, dan tabel absensi terbaru.
     */
    public function index()
    {
        $today = Carbon::today();

        $totalPegawai = Employee::where('status', 'Aktif')->count();

        $attendanceToday = Attendance::whereDate('date', $today)->get();

        $hadir = $attendanceToday->whereIn('status', ['hadir', 'alternatif'])->count();
        $terlambat = $attendanceToday->where('status', 'terlambat')->count();
        $tidakHadir = $attendanceToday->where('status', 'tidak_hadir')->count();
        $belumAbsen = max($totalPegawai - $attendanceToday->count(), 0);

        // Grafik kehadiran 6 hari terakhir (3 seri: hadir, terlambat, tidak hadir)
        $chartLabels = [];
        $chartHadir = [];
        $chartTerlambat = [];
        $chartTidakHadir = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $rows = Attendance::whereDate('date', $date)->get();

            $chartLabels[] = $date->translatedFormat('D');
            $chartHadir[] = $rows->whereIn('status', ['hadir', 'alternatif'])->count();
            $chartTerlambat[] = $rows->where('status', 'terlambat')->count();
            $chartTidakHadir[] = $rows->where('status', 'tidak_hadir')->count();
        }

        // Tabel absensi terbaru (10 data terakhir hari ini)
        $latestAttendance = Attendance::with('employee')
            ->whereDate('date', $today)
            ->latest('check_in')
            ->take(10)
            ->get()
            ->map(function (Attendance $a) {
                return [
                    'id' => $a->id,
                    'nama' => $a->employee->name ?? '-',
                    'departemen' => $a->employee->department ?? '-',
                    'jam' => $a->check_in ? $a->check_in->format('H:i') : '-',
                    'metode' => match ($a->method) {
                        'face_id' => 'Face ID',
                        'otp' => 'OTP',
                        default => '-',
                    },
                    'status' => match ($a->status) {
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'alternatif' => 'Alternatif',
                        default => 'Tidak Hadir',
                    },
                ];
            });

        return Inertia::render('Auth/Admin/Dashboard', [
            'stats' => [
                'totalPegawai' => $totalPegawai,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'tidakHadir' => $tidakHadir,
                'belumAbsen' => $belumAbsen,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'hadir' => $chartHadir,
                'terlambat' => $chartTerlambat,
                'tidakHadir' => $chartTidakHadir,
            ],
            'latestAttendance' => $latestAttendance,
        ]);
    }
}
