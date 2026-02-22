<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tax_brackets', function (Blueprint $table) {
            $table->id();
            $table->integer('faixa')->unique();
            $table->decimal('min_rbt12', 15, 2);
            $table->decimal('max_rbt12', 15, 2);
            $table->decimal('aliquota_nominal', 8, 4);
            $table->decimal('deducao', 15, 2);
            $table->decimal('irpj', 8, 4);
            $table->decimal('csll', 8, 4);
            $table->decimal('cofins', 8, 4);
            $table->decimal('pis', 8, 4);
            $table->decimal('cpp', 8, 4);
            $table->decimal('iss', 8, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};
