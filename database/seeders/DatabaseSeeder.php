<?php

namespace Database\Seeders;

use App\Models\SystemUser;
use App\Models\SystemUserPrivilege;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SystemUser::create([
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'middle_name' => 'A',
            'email' => 'admin@usep.edu.ph',
            'password_hash' => Hash::make('password123'),
            'role_type' => 'admin',
        ]);

        $admin = SystemUser::where('email', 'admin@usep.edu.ph')->first();

        SystemUserPrivilege::create([
            'system_user_id' => $admin->system_user_id,
            'can_manage_accounts' => true,
            'can_manage_equipment' => true,
            'can_manage_bookings' => true,
            'can_manage_logs' => true,
        ]);
    }
}
