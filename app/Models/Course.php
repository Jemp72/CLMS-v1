<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $primaryKey = 'course_id';

    protected $fillable = [
        'course_code',
        'course_name',
    ];

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'course_id');
    }
}