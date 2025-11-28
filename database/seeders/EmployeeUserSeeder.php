<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'employee@example.com'], // Ensure unique by email
            [
                'name' => 'Juan Dela Cruz',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                // 'middle_name' => 'S',
                'email' => 'employee@example.com',
                'password' => Hash::make('password123'), // default password
                'role' => User::ROLE_EMPLOYEE,
                'phone' => '09171234567',
                'purok_street' => 'Purok 1',
                'city_municipality' => 'Panabo City',
                'province' => 'Davao del Norte',
                'position' => 'Staff',
                'employment_status' => 'Permanent',
                'department' => 'HR',
                'status' => 'active',
                'birthday' => '1995-01-15',
                'must_change_password' => true,
                'employee_id' => 'EMP001',
                'verification_status' => 'verified',
            ]
        );

        $this->command->info('Employee user seeded successfully.');
    }
}
