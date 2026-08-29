<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Setting;
use App\Notifications\AttendanceRequestDecidedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    /**
     * Daftar pengajuan absensi alternatif (KF-07).
     */
    public function index()
    {
        $requests = AttendanceRequest::with('employee')
            ->latest()
            ->get()
            ->map(fn (AttendanceRequest $r) => $this->transform($r));

        return Inertia::render('Auth/Admin/Approval/Index', [
            'approvals' => $requests,
            'stats' => [
                'pending' => AttendanceRequest::where('status', 'pending')->count(),
                'approved_active' => AttendanceRequest::where('status', 'approved')
                    ->where('approval_type', 'sementara')->count(),
                'rejected' => AttendanceRequest::where('status', 'rejected')->count(),
            ],
        ]);
    }

    /**
     * Detail satu pengajuan, termasuk bukti pendukung (KF-06/KF-07).
     */
    public function show(AttendanceRequest $persetujuan_absensi)
    {
        return Inertia::render('Auth/Admin/Approval/Show', [
            'approval' => $this->transform($persetujuan_absensi, true),
        ]);
    }

    /**
     * Menyetujui pengajuan absensi alternatif.
     */
    public function approve(Request $request, AttendanceRequest $persetujuan_absensi)
    {
        $validated = $request->validate([
            'approval_type' => 'required|in:sementara,permanen',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $persetujuan_absensi->update([
            'status' => 'approved',
            'approval_type' => $validated['approval_type'],
            'admin_note' => $validated['admin_note'] ?? null,
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        // Catat sebagai absensi hari ini dengan metode alternatif (OTP)
        Attendance::updateOrCreate(
            [
                'employee_id' => $persetujuan_absensi->employee_id,
                'date' => now()->toDateString(),
            ],
            [
                'method' => 'otp',
                'status' => 'alternatif',
                'check_in' => now(),
            ]
        );

        $this->notifyEmployee($persetujuan_absensi);

        return redirect()
            ->route('admin.approvals.index')
            ->with('success', 'Pengajuan absensi alternatif disetujui.');
    }

    /**
     * Menolak pengajuan absensi alternatif.
     */
    public function reject(Request $request, AttendanceRequest $persetujuan_absensi)
    {
        $validated = $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $persetujuan_absensi->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        $this->notifyEmployee($persetujuan_absensi);

        return redirect()
            ->route('admin.approvals.index')
            ->with('success', 'Pengajuan absensi alternatif ditolak.');
    }

    /**
     * Kirim notifikasi keputusan ke pegawai (KF-12), kecuali toggle
     * notifikasi dimatikan di halaman Pengaturan, atau pegawai belum
     * memiliki akun user.
     */
    private function notifyEmployee(AttendanceRequest $r): void
    {
        if (Setting::get('notify_employee_decision', '1') !== '1') {
            return;
        }

        $r->loadMissing('employee.user');

        $r->employee?->user?->notify(new AttendanceRequestDecidedNotification($r));
    }

    private function transform(AttendanceRequest $r, bool $detail = false): array
    {
        $data = [
            'id' => $r->id,
            'name' => $r->employee->name ?? '-',
            'nip' => $r->employee->nip ?? '-',
            'department' => $r->employee->department ?? '-',
            'position' => $r->employee->position ?? '-',
            'shift' => $r->employee->shift ?? '-',
            'reason' => $r->reason,
            'date' => optional($r->created_at)->translatedFormat('d F Y'),
            'status' => match ($r->status) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => 'Menunggu',
            },
            'type' => $r->approval_type ? ucfirst($r->approval_type) : null,
        ];

        if ($detail) {
            $data['selfie_photo'] = $r->selfie_photo ? asset('storage/'.$r->selfie_photo) : null;
            $data['id_card_photo'] = $r->id_card_photo ? asset('storage/'.$r->id_card_photo) : null;
            $data['admin_note'] = $r->admin_note;
        }

        return $data;
    }
}
