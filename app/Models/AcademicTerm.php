<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $table = 'academic_terms';

    protected $primaryKey = 'academic_term_id';

    public $timestamps = false;

    protected $fillable = [
        'academic_year',
        'semester',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'term_id');
    }
}