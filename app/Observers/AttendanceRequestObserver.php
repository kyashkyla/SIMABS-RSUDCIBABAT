<?php

namespace App\Observers;

use App\Models\AttendanceRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewAttendanceRequestNotification;
use Illuminate\Support\Facades\Notification;

class AttendanceRequestObserver
{
    /**
     * Setiap kali ada pengajuan absensi alternatif baru,
     * beri tahu seluruh akun admin (KF-12 - Notifikasi Sistem),
     * kecuali toggle notifikasi dimatikan di halaman Pengaturan.
     */
    public function created(AttendanceRequest $attendanceRequest): void
    {
        if (Setting::get('notify_admin_new_request', '1') !== '1') {
            return;
        }

        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new NewAttendanceRequestNotification($attendanceRequest));
    }
}
