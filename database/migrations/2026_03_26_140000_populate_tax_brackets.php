<?php

use App\Models\TaxBracketVersion;
use App\Services\TaxBracketComparatorService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verifica se já existem dados na tabela
        if (DB::table('tax_brackets')->count() > 0) {
            return;
        }

        // Dados das faixas tributárias - Anexo III Simples Nacional
        $data = [
            [
                'faixa' => 1,
                'min_rbt12' => 0,
                'max_rbt12' => 180000,
                'aliquota_nominal' => 6.00,
                'deducao' => 0,
                'irpj' => 4,
                'csll' => 3.5,
                'cofins' => 12.82,
                'pis' => 2.78,
                'cpp' => 43.4,
                'iss' => 33.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faixa' => 2,
                'min_rbt12' => 180000.01,
                'max_rbt12' => 360000,
                'aliquota_nominal' => 11.20,
                'deducao' => 9360,
                'irpj' => 4,
                'csll' => 3.5,
                'cofins' => 14.05,
                'pis' => 3.05,
                'cpp' => 43.4,
                'iss' => 32,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faixa' => 3,
                'min_rbt12' => 360000.01,
                'max_rbt12' => 720000,
                'aliquota_nominal' => 13.50,
                'deducao' => 17640,
                'irpj' => 4,
                'csll' => 3.5,
                'cofins' => 13.64,
                'pis' => 2.96,
                'cpp' => 43.4,
                'iss' => 32.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faixa' => 4,
                'min_rbt12' => 720000.01,
                'max_rbt12' => 1800000,
                'aliquota_nominal' => 16.00,
                'deducao' => 35640,
                'irpj' => 4,
                'csll' => 3.5,
                'cofins' => 14.1,
                'pis' => 3.05,
                'cpp' => 43.4,
                'iss' => 31.95,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faixa' => 5,
                'min_rbt12' => 1800000.01,
                'max_rbt12' => 3600000,
                'aliquota_nominal' => 21.00,
                'deducao' => 125640,
                'irpj' => 4,
                'csll' => 3.5,
                'cofins' => 14.42,
                'pis' => 3.13,
                'cpp' => 43.4,
                'iss' => 31.55,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faixa' => 6,
                'min_rbt12' => 3600000.01,
                'max_rbt12' => 4800000,
                'aliquota_nominal' => 33.00,
                'deducao' => 648000,
                'irpj' => 35,
                'csll' => 15,
                'cofins' => 16.03,
                'pis' => 3.47,
                'cpp' => 30.5,
                'iss' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tax_brackets')->insert($data);

        // Registra versão
        TaxBracketVersion::create([
            'version' => 1,
            'source' => 'migration',
            'payload' => $data,
            'checksum' => TaxBracketComparatorService::computeChecksum($data),
            'applied_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tax_brackets')->delete();
        DB::table('tax_bracket_versions')->where('version', 1)->delete();
    }
};
