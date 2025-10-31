<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\supir;

class supirs extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        supir::create([
            'nama_supir' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'Aktif',
        ]);
        supir::create([
            'nama_supir' => 'Andi Wijaya',
            'no_hp' => '081234567891',
            'status' => 'Aktif',
        ]);
        supir::create([
            'nama_supir' => 'Slamet Riyadi',
            'no_hp' => '081234567892',
            'status' => 'Aktif',
        ]);
        supir::create([
            'nama_supir' => 'ajo maman',
            'no_hp' => '081234567893',
            'status' => 'Aktif',
        ]);
    }
}
