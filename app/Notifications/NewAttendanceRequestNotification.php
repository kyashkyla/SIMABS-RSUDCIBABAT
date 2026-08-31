<?php

namespace App\Notifications;

use App\Models\AttendanceRequest;
use Illuminate\Notifications\Notification;

class NewAttendanceRequestNotification extends Notification
{
    public function __construct(public AttendanceRequest $attendanceRequest)
    {
    }

    /**
     * Hanya disimpan ke database (ditampilkan di lonceng notifikasi admin).
     * Bisa ditambah channel 'mail' di kemudian hari bila diperlukan.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $employeeName = $this->attendanceRequest->employee->name ?? 'Pegawai';

        return [
            'title' => 'Pengajuan Absensi Alternatif Baru',
            'message' => "{$employeeName} mengajukan absensi alternatif dan menunggu persetujuan.",
            'url' => route('admin.approvals.show', $this->attendanceRequest->id),
        ];
    }
}
