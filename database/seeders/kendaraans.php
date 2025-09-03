<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\kendaraan;

class kendaraans extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        kendaraan::create([
            'plat_nomor' => 'B 1234 ABC',
            'id_supir' => '1',
            'status' => 'Tersedia',
        ]);
        kendaraan::create([
            'plat_nomor' => 'B 1232 ABC',
            'id_supir' => '2',
            'status' => 'Tersedia',
        ]);
        kendaraan::create([
            'plat_nomor' => 'B 1231 ABC',
            'id_supir' => '3',
            'status' => 'Tersedia',
        ]);
        kendaraan::create([
            'plat_nomor' => 'B 1334 ABC',
            'id_supir' => '4',
            'status' => 'Tidek Tersedia',
        ]);
    }
}
