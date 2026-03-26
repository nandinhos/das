<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usuário 1: Nando Dev
        User::firstOrCreate(
            ['email' => 'nandinhos@gmail.com'],
            [
                'name' => 'Nando Dev',
                'password' => Hash::make('Aer0G@cembrar'),
            ]
        );

        // Usuário 2: Angelica Domingos
        User::firstOrCreate(
            ['email' => 'angelica.domingos@hotmail.com'],
            [
                'name' => 'Angelica Domingos',
                'password' => Hash::make('kinnuty21star'),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove os usuários criados
        User::where('email', 'nandinhos@gmail.com')->delete();
        User::where('email', 'angelica.domingos@hotmail.com')->delete();
    }
};
