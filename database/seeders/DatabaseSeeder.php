<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin SIMRS',
            'email' => 'simrspr@gmail.com',
            'password' => bcrypt('testprogramer1'),
            'role' => 'admin',
        ]);

        // Create dokter user
        User::factory()->create([
            'name' => 'Dr. Ahmad',
            'email' => 'dokter@simrs.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        // Create petugas user
        User::factory()->create([
            'name' => 'Petugas SIMRS',
            'email' => 'petugas@simrs.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        echo "Users seeded successfully!\n";
        echo "Admin: simrspr@gmail.com / testprogramer1\n";
        echo "Dokter: dokter@simrs.com / password\n";
        echo "Petugas: petugas@simrs.com / password\n";
        
        // Seed Poli data
        $this->call(PoliSeeder::class);
        
        // Seed Obat data
        $this->call(ObatSeeder::class);
    }
}
