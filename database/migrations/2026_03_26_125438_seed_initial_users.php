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
        // Senhas vêm do ambiente (ADMIN_*_PASSWORD) — nunca hardcoded no código.
        // Sem valor configurado, o usuário não é criado pela migração (fail loud).
        $nandoPassword = env('ADMIN_NANDO_PASSWORD');
        if ($nandoPassword) {
            // Usuário 1: Nando Dev
            User::firstOrCreate(
                ['email' => 'nandinhos@gmail.com'],
                [
                    'name' => 'Nando Dev',
                    'password' => Hash::make($nandoPassword),
                ]
            );
        }

        $angelicaPassword = env('ADMIN_ANGELICA_PASSWORD');
        if ($angelicaPassword) {
            // Usuário 2: Angelica Domingos
            User::firstOrCreate(
                ['email' => 'angelica.domingos@hotmail.com'],
                [
                    'name' => 'Angelica Domingos',
                    'password' => Hash::make($angelicaPassword),
                ]
            );
        }
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
