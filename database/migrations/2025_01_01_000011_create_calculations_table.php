<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            // Dados do período
            $table->decimal('rpa', 15, 2);          // Receita do Período de Apuração
            $table->decimal('rbt12', 15, 2);         // Receita Bruta 12 meses anteriores
            $table->json('rbt12_data');              // [{month, year, amount}, ...]

            // Faixa tributária identificada
            $table->unsignedTinyInteger('tax_bracket');    // 1-6
            $table->decimal('aliquota_nominal', 8, 5);    // ex: 0.06000
            $table->decimal('parcela_deduzir', 12, 2);    // ex: 9360.00

            // Resultado
            $table->decimal('aliquota_efetiva', 8, 5);   // ex: 0.05432
            $table->decimal('valor_total_das', 12, 2);   // Valor final DAS
            $table->boolean('special_case')->default(false); // RBT12 ≤ 180k

            // Repartição dos tributos
            $table->decimal('irpj_percent', 8, 5);
            $table->decimal('irpj_value', 12, 2);
            $table->decimal('csll_percent', 8, 5);
            $table->decimal('csll_value', 12, 2);
            $table->decimal('cofins_percent', 8, 5);
            $table->decimal('cofins_value', 12, 2);
            $table->decimal('pis_percent', 8, 5);
            $table->decimal('pis_value', 12, 2);
            $table->decimal('cpp_percent', 8, 5);
            $table->decimal('cpp_value', 12, 2);
            $table->decimal('iss_percent', 8, 5);
            $table->decimal('iss_value', 12, 2);

            $table->timestamps();

            // Um cálculo salvo por mês/ano
            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
