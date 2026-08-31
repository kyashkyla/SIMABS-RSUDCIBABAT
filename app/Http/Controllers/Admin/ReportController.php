<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Laporan absensi harian / mingguan / bulanan / rentang tanggal (KF-11).
     */
    public function index(Request $request)
    {
        [$start, $end] = $this->resolveRange($request);

        $attendances = Attendance::with('employee')
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->when($request->filled('department'), fn ($q) => $q->whereHas(
                'employee',
                fn ($eq) => $eq->where('department', $request->department)
            ))
            ->get();

        $summary = [
            'hadir' => $attendances->whereIn('status', ['hadir', 'alternatif'])->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'tidak_hadir' => $attendances->where('status', 'tidak_hadir')->count(),
            'total' => $attendances->count(),
        ];

        $records = $attendances->map(function (Attendance $a) {
            return [
                'id' => $a->id,
                'date' => $a->attendance_date->translatedFormat('d F Y'),
                'nip' => $a->employee->nip ?? '-',
                'name' => $a->employee->name ?? '-',
                'department' => $a->employee->department ?? '-',
                'shift' => $a->employee->shift ?? '-',
                'checkIn' => $a->check_in_at ? $a->check_in_at->format('H:i') : '-',
                'checkOut' => $a->check_out_at ? $a->check_out_at->format('H:i') : '-',
                'method' => match ($a->check_in_method) {
                    'face' => 'Face ID',
                    'otp' => 'OTP',
                    'alternative' => 'Alternatif',
                    default => '-',
                },
                'status' => match ($a->status) {
                    'hadir' => 'Hadir',
                    'terlambat' => 'Terlambat',
                    'alternatif' => 'Alternatif',
                    default => 'Tidak Hadir',
                },
            ];
        })->values();

        return Inertia::render('Auth/Admin/Report/Index', [
            'summary' => $summary,
            'records' => $records,
            'departments' => Employee::select('department')->distinct()->pluck('department'),
            'filters' => [
                'period' => $request->get('period', 'harian'),
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
        ]);
    }

    /**
     * Export laporan ke CSV (mudah dibuka di Excel).
     */
    public function export(Request $request)
    {
        [$start, $end] = $this->resolveRange($request);

        $attendances = Attendance::with('employee')
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->when($request->filled('department'), fn ($q) => $q->whereHas(
                'employee',
                fn ($eq) => $eq->where('department', $request->department)
            ))
            ->orderBy('attendance_date')
            ->get();

        $filename = 'laporan-absensi-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        $callback = function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Tanggal', 'NIP', 'Nama', 'Departemen', 'Jam Masuk', 'Jam Keluar', 'Metode', 'Status']);

            foreach ($attendances as $a) {
                fputcsv($handle, [
                    $a->attendance_date->format('Y-m-d'),
                    $a->employee->nip ?? '-',
                    $a->employee->name ?? '-',
                    $a->employee->department ?? '-',
                    $a->check_in_at ? $a->check_in_at->format('H:i') : '-',
                    $a->check_out_at ? $a->check_out_at->format('H:i') : '-',
                    $a->check_in_method ?? '-',
                    $a->status,
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Export laporan ke PDF menggunakan barryvdh/laravel-dompdf.
     */
    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->resolveRange($request);

        $attendances = Attendance::with('employee')
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->when($request->filled('department'), fn ($q) => $q->whereHas(
                'employee',
                fn ($eq) => $eq->where('department', $request->department)
            ))
            ->orderBy('attendance_date')
            ->get();

        $summary = [
            'hadir' => $attendances->whereIn('status', ['hadir', 'alternatif'])->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'tidak_hadir' => $attendances->where('status', 'tidak_hadir')->count(),
            'total' => $attendances->count(),
        ];

        $records = $attendances->map(fn (Attendance $a) => [
            'date' => $a->attendance_date->translatedFormat('d M Y'),
            'nip' => $a->employee->nip ?? '-',
            'name' => $a->employee->name ?? '-',
            'department' => $a->employee->department ?? '-',
            'checkIn' => $a->check_in_at ? $a->check_in_at->format('H:i') : '-',
            'checkOut' => $a->check_out_at ? $a->check_out_at->format('H:i') : '-',
            'method' => match ($a->check_in_method) {
                'face' => 'Face ID',
                'otp' => 'OTP',
                'alternative' => 'Alternatif',
                default => '-',
            },
            'status' => match ($a->status) {
                'hadir' => 'Hadir',
                'terlambat' => 'Terlambat',
                'alternatif' => 'Alternatif',
                default => 'Tidak Hadir',
            },
        ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'summary' => $summary,
            'records' => $records,
            'start' => $start->translatedFormat('d F Y'),
            'end' => $end->translatedFormat('d F Y'),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-absensi-'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Menentukan rentang tanggal laporan berdasarkan periode yang dipilih.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveRange(Request $request): array
    {
        $period = $request->get('period', 'harian');

        if ($request->filled('start') && $request->filled('end')) {
            return [Carbon::parse($request->start), Carbon::parse($request->end)];
        }

        return match ($period) {
            'mingguan' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'bulanan' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            default => [Carbon::today(), Carbon::today()],
        };
    }
}