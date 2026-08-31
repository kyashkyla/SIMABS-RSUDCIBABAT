<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai dibaca, lalu arahkan ke halaman terkait
     * (misalnya detail pengajuan absensi alternatif).
     */
    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return back();
    }

    /**
     * Tandai seluruh notifikasi sebagai dibaca.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
