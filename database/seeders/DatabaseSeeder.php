<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            AdminSeeder::class,
            PetugasSeeder::class,
            // PenumpangSeeder::class,
            rutes::class,
            jadwals::class,
            kursis::class,
        ]);
        // User::create([
        //     'name' => 'Admin',
        //     'username' => 'admin',
        //     'password' => Hash::make('admin123'),
        //     'role' => 'admin',
        // ]);

        // User::create([
        //     'name' => 'Petugas 1',
        //     'username' => 'petugas',
        //     'password' => Hash::make('petugas123'),
        //     'role' => 'petugas',
        // ]);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
