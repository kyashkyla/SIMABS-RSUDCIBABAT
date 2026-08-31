<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    protected $otpCode;

    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Kode OTP Absensi',
            'message' => 'Kode OTP Anda adalah: ' . $this->otpCode . '. Berlaku selama 3 menit.',
        ];
    }
}
