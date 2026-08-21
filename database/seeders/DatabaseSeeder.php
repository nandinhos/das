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
        // Senhas vêm do ambiente (ADMIN_*_PASSWORD) — nunca hardcoded no código.
        $nandoPassword = env('ADMIN_NANDO_PASSWORD');
        if ($nandoPassword) {
            // Usuário 1: Nando Dev
            User::factory()->create([
                'name' => 'Nando Dev',
                'email' => 'nandinhos@gmail.com',
                'password' => bcrypt($nandoPassword),
            ]);
        }

        $angelicaPassword = env('ADMIN_ANGELICA_PASSWORD');
        if ($angelicaPassword) {
            // Usuário 2: Angelica Domingos
            User::factory()->create([
                'name' => 'Angelica Domingos',
                'email' => 'angelica.domingos@hotmail.com',
                'password' => bcrypt($angelicaPassword),
            ]);
        }

        $this->call([
            TaxBracketSeeder::class,
        ]);
    }
}
