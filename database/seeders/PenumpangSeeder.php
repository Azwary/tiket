<?php

namespace Database\Seeders;

use App\Models\Penumpang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenumpangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Penumpang::create([
            'nama_penumpang' => 'Penumpang 1',
            'no_telepon' => '081234567890',
            'username' => 'penumpang',
            'password' => Hash::make('penumpang123'),
        ]);
    }
}
