<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Optional: create a test employee user
        // \App\Models\User::factory()->create([
        //     'first_name' => 'Test',
        //     'last_name' => 'User',
        //     'email' => 'test@example.com',
        //     'employee_id' => 'EMP001',
        //     'role' => 'employee',
        //     'password' => bcrypt('password'),
        // ]);

        // Seed regions → provinces → cities → barangays
        // $this->call([
        //     RegionSeeder::class,
        //     ProvinceSeeder::class,
        //     CitySeeder::class,
        //     BarangaySeeder::class,
        // ]);

        // Seed admin user
        $this->call(AdminUserSeeder::class);
        $this->call(EmployeeUserSeeder::class);
    }
}
