<?php

namespace App\Models;

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

    public function todayAttendance(): ?Attendance
    {
        return $this->attendances()
            ->whereDate('attendance_date', now()->toDateString())
            ->first();
    }
}