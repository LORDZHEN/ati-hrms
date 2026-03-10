<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run()
    {
        $cities = [
            'Davao del Norte' => [
                // Cities
                'Island Garden City of Samal',
                'Panabo City',
                'Tagum City',
                // Municipalities
                'Asuncion',
                'Braulio E. Dujali',
                'Carmen',
                'Kapalong',
                'New Corella',
                'San Isidro',
                'Santo Tomas',
                'Talaingod',
            ],

            'Davao del Sur' => [
                // City
                'Digos City',
                // Municipalities
                'Bansalan',
                'Hagonoy',
                'Kiblawan',
                'Magsaysay',
                'Malalag',
                'Matanao',
                'Padada',
                'Santa Cruz',
                'Sulop',
            ],

            'Davao de Oro' => [
                // Municipalities (no cities)
                'Compostela',
                'Laak',
                'Mabini',
                'Maco',
                'Maragusan',
                'Mawab',
                'Monkayo',
                'Nabunturan',
                'New Bataan',
                'Pantukan',
                'Montevista',
            ],

            'Davao Occidental' => [
                // Municipalities (no cities)
                'Don Marcelino',
                'Jose Abad Santos',
                'Malita',
                'Santa Maria',
                'Sarangani',
            ],

            'Davao Oriental' => [
                // City
                'Mati City',
                // Municipalities
                'Baganga',
                'Banaybanay',
                'Boston',
                'Caraga',
                'Cateel',
                'Governor Generoso',
                'Lupon',
                'Manay',
                'San Isidro',
                'Tarragona',
            ],

            'Davao City' => [
                // Davao City is a highly urbanized city (HUC) and is usually listed separately
                // It has no municipalities under it, only districts/barangays
                // If you want to include it as a standalone entry:
                'Davao City',
            ],
        ];

        foreach ($cities as $provinceName => $cityList) {
            $province = Province::where('name', $provinceName)->first();
            if ($province) {
                foreach ($cityList as $city) {
                    City::create([
                        'province_id' => $province->id,
                        'name' => $city
                    ]);
                }
            }
        }
    }
}
