<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            [
                'email' => 'admin@hrms.com',
            ],
            [
                'name' => 'System Administrator',
                'password'   => Hash::make('password123'),
                'role'       => 'admin',
                'verification_status' => 'verified',
                'status'     => 'active',
                'must_change_password' => false,
                'phone' => '09123456789',
                'purok_street' => 'Main Street',
                'city_municipality' => 'Panabo City',
                'province' => 'Davao del Norte',
                'birthday' => '1990-01-01',
            ]
        );
    }
}
