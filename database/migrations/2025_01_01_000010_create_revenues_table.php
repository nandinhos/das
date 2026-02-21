<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');   // 1-12
            $table->unsignedSmallInteger('year');   // ex: 2025
            $table->decimal('amount', 15, 2);       // Receita Bruta do Período (RPA)
            $table->timestamps();

            // Uma receita por mês/ano
            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
