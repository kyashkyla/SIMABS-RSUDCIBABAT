<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Riwayat absensi seluruh pegawai (KF-10), bisa difilter
     * berdasarkan tanggal, unit kerja (department), dan nama pegawai.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('employee')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('date', $request->date))
            ->when($request->filled('department'), fn ($q) => $q->whereHas(
                'employee',
                fn ($eq) => $eq->where('department', $request->department)
            ))
            ->when($request->filled('search'), fn ($q) => $q->whereHas(
                'employee',
                fn ($eq) => $eq->where('name', 'like', '%'.$request->search.'%')
            ))
            ->latest('date');

        $attendances = $query->paginate(15)->withQueryString();

        $attendances->getCollection()->transform(function (Attendance $a) {
            return [
                'id' => $a->id,
                'name' => $a->employee->name ?? '-',
                'nip' => $a->employee->nip ?? '-',
                'department' => $a->employee->department ?? '-',
                'date' => $a->date->translatedFormat('d F Y'),
                'checkIn' => $a->check_in ? $a->check_in->format('H:i') : '-',
                'checkOut' => $a->check_out ? $a->check_out->format('H:i') : '-',
                'method' => match ($a->method) {
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

        return Inertia::render('Auth/Admin/Attendance/History', [
            'attendances' => $attendances,
            'departments' => Employee::select('department')->distinct()->pluck('department'),
            'filters' => $request->only(['date', 'department', 'search']),
        ]);
    }
}
