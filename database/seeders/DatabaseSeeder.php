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
        // Brand Seeder
        $this->call(BrandSeeder::class);
        $this->call(EquivalenceDoorsSeeder::class);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'puigadmin@gmail.com',
            'password' => bcrypt('Puig2024'),
        ]);
    }
}
