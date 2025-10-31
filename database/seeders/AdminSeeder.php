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
            'tanggal_lahir' => '2000-01-01',
        ]);
        Admin::create([
            'nama_admin' => 'Ajo',
            'username' => 'padang',
            'password' => Hash::make('padang123'),
            'role' => 'padang',
            'tanggal_lahir' => '2000-02-02',
        ]);
        Admin::create([
            'nama_admin' => 'Mamak',
            'username' => 'solok',
            'password' => Hash::make('solok123'),
            'role' => 'solok',
            'tanggal_lahir' => '2000-03-03',
        ]);
        Admin::create([
            'nama_admin' => 'si jo',
            'username' => 'sawahlunto',
            'password' => Hash::make('sawahlunto123'),
            'role' => 'sawah_lunto',
            'tanggal_lahir' => '2000-03-03',
        ]);
    }
}
