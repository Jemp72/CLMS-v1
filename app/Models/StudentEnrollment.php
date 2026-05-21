<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $table = 'student_enrollment';

    protected $primaryKey = 'enrollment_id';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'class_schedule_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }
}