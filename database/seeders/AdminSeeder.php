<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'nama_admin' => 'Mamat',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'umum',
        ]);
        Admin::create([
            'nama_admin' => 'Ajo',
            'username' => 'padang',
            'password' => Hash::make('padang123'),
            'role' => 'padang',
        ]);
        Admin::create([
            'nama_admin' => 'Mamak',
            'username' => 'solok',
            'password' => Hash::make('solok123'),
            'role' => 'solok',
        ]);
        Admin::create([
            'nama_admin' => 'si jo',
            'username' => 'sawahlunto',
            'password' => Hash::make('sawahlunto123'),
            'role' => 'sawah_lunto',
        ]);
    }
}
