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
                'email' => 'ati.rtrc.hr@gmail.com',
            ],
            [
                'name' => 'System Administrator',
                'password'   => Hash::make('password123'),
                'role'       => 'admin',
                'verification_status' => 'verified',
                'status'     => 'active',
                'must_change_password' => false,
                'phone' => '09123456789',
                'barangay_id' => 'Gredu',
                'city_id' => 'Panabo',
                'province_id' => 'Davao Del Norte',
                'region_id' => 'Region XI',
                'birthday' => '1990-01-01',
            ]
        );
    }
}
