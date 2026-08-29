<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_at',
        'check_in_lat',
        'check_in_lng',
        'check_in_distance_meters',
        'check_in_method',
        'check_in_photo',
        'check_out_at',
        'check_out_lat',
        'check_out_lng',
        'check_out_distance_meters',
        'check_out_method',
        'check_out_photo',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_in_lat' => 'float',
        'check_in_lng' => 'float',
        'check_out_at' => 'datetime',
        'check_out_lat' => 'float',
        'check_out_lng' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}