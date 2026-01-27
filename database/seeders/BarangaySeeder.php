<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Barangay;

class BarangaySeeder extends Seeder
{
    public function run()
    {
        $barangays = [
            'Carmen' => [
                'Alejal',
                'Anibongan',
                'Asuncion',
                'Cebulano',
                'Guadalupe',
                'Ising',
                'La Paz',
                'Mabaus',
                'Mabuhay',
                'Magsaysay',
                'Mangalcal',
                'Minda',
                'New Camiling',
                'Salvacion',
                'San Isidro',
                'Santo Niño',
                'Taba',
                'Tibulao',
                'Tubod',
                'Tuganay'
            ],

            'Panabo City' => [
                'A. O. Floirendo',
                'Datu Abdul Dadia',
                'Buenavista',
                'Cacao',
                'Cagangohan',
                'Consolacion',
                'Dapco',
                'Gredu',
                'J.P. Laurel',
                'Kasilak',
                'Katipunan',
                'Katualan',
                'Kauswagan',
                'Kiotoy',
                'Little Panay',
                'Lower Panaga (Roxas)',
                'Mabunao',
                'Maduao',
                'Malativas',
                'Manay',
                'Nanyo',
                'New Malaga (Dalisay)',
                'New Malitbog',
                'New Pandan (Poblacion)',
                'New Visayas',
                'Quezon',
                'Salvacion',
                'San Francisco (Poblacion)',
                'San Nicolas',
                'San Pedro',
                'San Roque',
                'San Vicente',
                'Santa Cruz',
                'Santo Niño (Poblacion)',
                'Sindaton',
                'Southern Davao',
                'Tagpore',
                'Tibungol',
                'Upper Licanan',
                'Waterfall'
            ],

            // Add other cities...
        ];

        foreach ($barangays as $cityName => $bgyList) {
            $city = City::where('name', $cityName)->first();
            if ($city) {
                foreach ($bgyList as $bgy) {
                    Barangay::create([
                        'city_id' => $city->id,
                        'name' => $bgy
                    ]);
                }
            }
        }
    }
}
