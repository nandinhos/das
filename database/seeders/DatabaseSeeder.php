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
        //
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
