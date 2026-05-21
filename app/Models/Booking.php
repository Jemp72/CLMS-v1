<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $primaryKey = 'booking_id';

    public $timestamps = false;

    protected $fillable = [
        'lab_id',
        'requestor_name',
        'contact_number',
        'purpose',
        'booking_date',
        'start_time',
        'end_time',
        'booking_status',
        'approved_by',
        'created_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }

    public function approver()
    {
        return $this->belongsTo(SystemUser::class, 'approved_by');
    }
}