<?php

namespace App\Notifications;

use App\Models\AttendanceRequest;
use Illuminate\Notifications\Notification;

class AttendanceRequestDecidedNotification extends Notification
{
    public function __construct(public AttendanceRequest $attendanceRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->attendanceRequest->status === 'approved';

        return [
            'title' => $isApproved
                ? 'Pengajuan Absensi Alternatif Disetujui'
                : 'Pengajuan Absensi Alternatif Ditolak',
            'message' => $this->attendanceRequest->admin_note
                ?: ($isApproved ? 'Pengajuan Anda telah disetujui oleh admin.' : 'Pengajuan Anda ditolak oleh admin.'),
            'url' => null, // diisi setelah modul Pegawai (riwayat pengajuan) dibuat
        ];
    }
}
