<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemUserPrivilege extends Model
{
    use HasFactory;

    protected $table = 'system_user_privileges';

    protected $primaryKey = 'privilege_id';

    protected $fillable = [
        'system_user_id',
        'can_manage_accounts',
        'can_manage_equipment',
        'can_manage_bookings',
        'can_manage_logs',
    ];

    public function user()
    {
        return $this->belongsTo(SystemUser::class, 'system_user_id');
    }
}