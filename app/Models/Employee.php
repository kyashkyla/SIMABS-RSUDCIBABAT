<?php

namespace App\Models;

use App\Support\ShiftSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'nik',
        'email',
        'phone',
        'gender',
        'position',
        'department',
        'status',
        'start_date',
        'shift',
        'face_id_registered',
        'photo',
    ];

    protected $casts = [
        'start_date' => 'date',
        'face_id_registered' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Detail jam shift pegawai ini (mulai, selesai, jendela absen masuk
     * & pulang, batas terlambat) — dihitung dari config/attendance.php.
     */
    public function shiftDefinition(): array
    {
        return ShiftSchedule::definition($this->shift);
    }

    /**
     * Apakah shift pegawai ini melewati tengah malam (mis. shift Malam
     * 23:00 - 07:00). Kalau ya, jendela absen pulangnya jatuh pada
     * tanggal setelah tanggal absen masuknya.
     */
    public function hasOvernightShift(): bool
    {
        return $this->shiftDefinition()['overnight'] ?? false;
    }

    /**
     * Absensi milik pegawai ini yang masih "berjalan" hari ini.
     *
     * Untuk shift Pagi/Siang, ini cukup dicari berdasarkan tanggal hari
     * ini. Tapi untuk shift Malam (23:00 - 07:00), absen masuk dicatat
     * dengan tanggal KEMARIN sedangkan absen pulangnya baru dilakukan
     * dini hari INI — jadi kalau tidak ketemu data untuk hari ini, dan
     * shift pegawai melewati tengah malam, cek juga absensi kemarin yang
     * belum di-checkout.
     */
    public function todayAttendance(): ?Attendance
    {
        $today = $this->attendances()
            ->whereDate('attendance_date', now()->toDateString())
            ->first();

        if ($today) {
            return $today;
        }

        if ($this->hasOvernightShift()) {
            return $this->attendances()
                ->whereDate('attendance_date', now()->copy()->subDay()->toDateString())
                ->whereNull('check_out_at')
                ->first();
        }

        return null;
    }

    public function attendanceRequests(): HasMany
    {
        return $this->hasMany(AttendanceRequest::class);
    }
}