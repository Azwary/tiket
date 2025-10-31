<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Seeder;

class JadwalsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Padang - Solok (id_rute = 1)
            [1, '05:30'],
            [1, '06:30'],
            [1, '07:30'],
            [1, '08:30'],
            [1, '09:30'],
            [1, '10:30'],
            [1, '11:30'],
            [1, '12:30'],
            [1, '13:30'],
            [1, '14:30'],
            [1, '15:30'],
            [1, '16:30'],
            [1, '17:30'],

            // Padang - Sawahlunto (id_rute = 2)
            [2, '05:45'],
            [2, '06:45'],
            [2, '07:45'],
            [2, '08:45'],
            [2, '09:45'],
            [2, '10:45'],
            [2, '11:45'],
            [2, '12:45'],
            [2, '13:45'],
            [2, '14:45'],
            [2, '15:45'],
            [2, '16:45'],
            [2, '17:45'],

            // Sawahlunto - Padang (id_rute = 3)
            [3, '05:45'],
            [3, '06:45'],
            [3, '07:45'],
            [3, '08:45'],
            [3, '09:45'],
            [3, '10:45'],
            [3, '11:45'],
            [3, '12:45'],
            [3, '13:45'],
            [3, '14:45'],
            [3, '15:45'],
            [3, '16:45'],
            [3, '17:45'],

            // Solok - Padang (id_rute = 4)
            [4, '05:30'],
            [4, '06:30'],
            [4, '07:30'],
            [4, '08:30'],
            [4, '09:30'],
            [4, '10:30'],
            [4, '11:30'],
            [4, '12:30'],
            [4, '13:30'],
            [4, '14:30'],
            [4, '15:30'],
            [4, '16:30'],
            [4, '17:30'],
        ];

        foreach ($data as [$id_rute, $jam]) {
            $id_supir = \App\Models\Supir::inRandomOrder()->first()->id_supir;
            $id_kendaraan = \App\Models\Kendaraan::inRandomOrder()->first()->id_kendaraan;

            Jadwal::create([
                'id_rute' => $id_rute,
                'id_supir' => $id_supir,
                'id_kendaraan' => $id_kendaraan,
                'jam_keberangkatan' => $jam,
            ]);
        }
    }
}
