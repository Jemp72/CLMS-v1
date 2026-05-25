<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabUtilizationLog extends Model
{
    use HasFactory;

    protected $table = 'lab_utilization_logs';

    protected $primaryKey = 'lab_utilization_log_id';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'guest_log_id',
        'purpose',
        'instructor_id',
        'lab_id',
        'log_date',
        'time_in',
        'time_out',
    ];

    protected $casts = [
        'log_date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function guest()
    {
        return $this->belongsTo(GuestLog::class, 'guest_log_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }
}