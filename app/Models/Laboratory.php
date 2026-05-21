<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laboratory extends Model
{
    use HasFactory;

    protected $table = 'laboratories';

    protected $primaryKey = 'lab_id';

    protected $fillable = [
        'lab_name',
        'location',
        'capacity',
    ];

    public function logs()
    {
        return $this->hasMany(LabUtilizationLog::class, 'lab_id');
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'lab_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'lab_id');
    }
}