<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuário 1: Nando Dev
        User::factory()->create([
            'name' => 'Nando Dev',
            'email' => 'nandinhos@gmail.com',
            'password' => bcrypt('Aer0G@cembraer'),
        ]);

        // Usuário 2: Angelica Domingos
        User::factory()->create([
            'name' => 'Angelica Domingos',
            'email' => 'angelica.domingos@hotmail.com',
            'password' => bcrypt('kinnuty21star'),
        ]);

        $this->call([
            TaxBracketSeeder::class,
        ]);
    }
}
