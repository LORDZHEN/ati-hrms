<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            'Region I (Ilocos Region)',
            'Region II (Cagayan Valley)',
            'Region III (Central Luzon)',
            'Region IV-A (CALABARZON)',
            'Region IV-B (MIMAROPA)',
            'Region V (Bicol Region)',
            'Region VI (Western Visayas)',
            'Region VII (Central Visayas)',
            'Region VIII (Eastern Visayas)',
            'Region IX (Zamboanga Peninsula)',
            'Region X (Northern Mindanao)',
            'Region XI (Davao Region)',
            'Region XII (SOCCSKSARGEN)',
            'Region XIII (Caraga)',
            'BARMM (Bangsamoro Autonomous Region in Muslim Mindanao)',
            'NCR (National Capital Region)',
            'CAR (Cordillera Administrative Region)',
        ];

        foreach ($regions as $region) {
            Region::create(['name' => $region]);
        }
    }
}
