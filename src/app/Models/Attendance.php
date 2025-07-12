<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'status',
        'total_work_minutes',
    ];

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }

    public function approvedCorrectionRequest()
    {
        return $this->hasOne(AttendanceCorrectionRequest::class)
                ->where('status', 'approved');
    }
}