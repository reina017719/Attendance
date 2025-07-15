<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'requested_start_time',
        'requested_end_time',
        'requested_break1_start_time',
        'requested_break1_end_time',
        'requested_break2_start_time',
        'requested_break2_end_time',
        'reason',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakTime()
    {
        return $this->belongsTo(BreakTime::class, 'break_id');
    }
}