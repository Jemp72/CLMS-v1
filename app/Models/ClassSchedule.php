<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $table = 'class_schedule';

    protected $primaryKey = 'class_schedule_id';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'lab_id',
        'day_of_week',
        'time_start',
        'time_end',
        'term_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function instructor()
    {
        return $this->belongsTo(SystemUser::class, 'instructor_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'class_schedule_id');
    }
}