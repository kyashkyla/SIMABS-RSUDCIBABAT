<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'start_date' => 'date',
        'face_id_registered' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}