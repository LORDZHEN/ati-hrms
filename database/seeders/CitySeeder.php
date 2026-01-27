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
                'Panabo City',
                'Tagum City',
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
                'Digos City',
                'Bansalan',
                'Hagonoy',
                'Kiblawan',
                'Magsaysay',
                'Malalag',
                'Matanao',
                'Padada',
                'Santa Cruz',
            ],

            'Davao de Oro' => [
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
            ],

            'Davao Occidental' => [
                'Don Marcelino',
                'Jose Abad Santos',
                'Malita',
                'Santa Maria',
                'Sarangani',
                // 'Sulop', // optional: verify
            ],

            'Davao Oriental' => [
                'Mati City',
                'Baganga',
                'Boston',
                'Caraga',
                'Cateel',
                'Manay',
                'San Isidro',
                'Tarragona',
                'Bagumbayan',
                'Governor Generoso',
                'Lupon',
                'Pantukan',
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
