<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        $provinces = [
            'Region XI (Davao Region)' => [
                'Davao del Norte',
                'Davao del Sur',
                'Davao de Oro',
                'Davao Occidental',
                'Davao Oriental'
            ],
            // Add other regions here if desired
        ];

        foreach ($provinces as $regionName => $provList) {
            $region = Region::where('name', $regionName)->first();
            if ($region) {
                foreach ($provList as $prov) {
                    Province::create([
                        'region_id' => $region->id,
                        'name' => $prov
                    ]);
                }
            }
        }
    }
}
