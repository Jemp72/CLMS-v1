<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuestLog extends Model
{
    use HasFactory;

    protected $table = 'guest_logs';

    protected $primaryKey = 'guest_log_id';

    protected $fillable = [
        'guest_name',
        'organization',
        'contact_number',
        'purpose',
    ];

    public function utilizationLogs()
    {
        return $this->hasMany(LabUtilizationLog::class, 'guest_log_id');
    }
}