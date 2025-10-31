<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supir;

class supirs extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supir::create([
            'nama_supir' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'Aktif',
        ]);
        Supir::create([
            'nama_supir' => 'Andi Wijaya',
            'no_hp' => '081234567891',
            'status' => 'Aktif',
        ]);
        Supir::create([
            'nama_supir' => 'Slamet Riyadi',
            'no_hp' => '081234567892',
            'status' => 'Aktif',
        ]);
        Supir::create([
            'nama_supir' => 'ajo maman',
            'no_hp' => '081234567893',
            'status' => 'Aktif',
        ]);
    }
}
