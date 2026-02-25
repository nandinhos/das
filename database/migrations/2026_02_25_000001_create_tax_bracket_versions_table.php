<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_bracket_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('version');
            $table->string('source');        // 'seed' | 'scraper' | 'manual'
            $table->json('payload');         // array completo das 6 faixas
            $table->string('checksum', 64);  // SHA-256 do payload normalizado
            $table->timestamp('applied_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_bracket_versions');
    }
};
