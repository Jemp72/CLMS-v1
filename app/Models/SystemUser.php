<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemUser extends Authenticatable
{
    use HasFactory;

    protected $table = 'system_users';

    protected $primaryKey = 'system_user_id';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'email',
        'password_hash',
        'reset_token',
        'reset_token_expiration',
    ];

    protected $hidden = [
        'password_hash',
        'reset_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}