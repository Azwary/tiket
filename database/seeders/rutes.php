<?php

namespace Database\Seeders;

use App\Models\Rute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class rutes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rute::create([
            'asal' => 'Padang',
            'tujuan' => 'Solok',
            'harga' => 30000,
        ]);
        Rute::create([
            'asal' => 'Padang',
            'tujuan' => 'Sawah Lunto',
            'harga' => 40000,
        ]);
        Rute::create([
            'asal' => 'Sawah Lunto',
            'tujuan' => 'Padang',
            'harga' => 40000,
        ]);
        Rute::create([
            'asal' => 'Solok',
            'tujuan' => 'Padang',
            'harga' => 30000,
        ]);
    }
}
