<?php

namespace Database\Seeders;

use App\Models\Kursi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class kursis extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kursis = [
            ['no_kursi' => 1],
            ['no_kursi' => 2],
            ['no_kursi' => 3],
            ['no_kursi' => 4],
            ['no_kursi' => 5],
            ['no_kursi' => 6],
            ['no_kursi' => 7],
            ['no_kursi' => 8],
            ['no_kursi' => 9],
            ['no_kursi' => 10],
            ['no_kursi' => 11],
            ['no_kursi' => 12],
            ['no_kursi' => 13],
            ['no_kursi' => 14],
            ['no_kursi' => 15],
        ];

        foreach ($kursis as $kursi) {
            Kursi::create($kursi);
        }
    }
}
