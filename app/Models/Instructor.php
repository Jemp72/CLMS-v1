<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructors';

    protected $primaryKey = 'instructor_id';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'email',
        'contact_number',
    ];

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'instructor_id');
    }

    public function logs()
    {
        return $this->hasMany(LabUtilizationLog::class, 'instructor_id');
    }
}
