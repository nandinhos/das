<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxBracketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brackets = [
            [
                'faixa' => 1,
                'min_rbt12' => 0,
                'max_rbt12' => 180000,
                'aliquota_nominal' => 0.06,
                'deducao' => 0,
                'irpj' => 0.04,
                'csll' => 0.035,
                'cofins' => 0.1282,
                'pis' => 0.0278,
                'cpp' => 0.434,
                'iss' => 0.335,
            ],
            [
                'faixa' => 2,
                'min_rbt12' => 180000.01,
                'max_rbt12' => 360000,
                'aliquota_nominal' => 0.112,
                'deducao' => 9360,
                'irpj' => 0.04,
                'csll' => 0.035,
                'cofins' => 0.1405,
                'pis' => 0.0305,
                'cpp' => 0.434,
                'iss' => 0.32,
            ],
            [
                'faixa' => 3,
                'min_rbt12' => 360000.01,
                'max_rbt12' => 720000,
                'aliquota_nominal' => 0.135,
                'deducao' => 17640,
                'irpj' => 0.04,
                'csll' => 0.035,
                'cofins' => 0.1364,
                'pis' => 0.0296,
                'cpp' => 0.434,
                'iss' => 0.325,
            ],
            [
                'faixa' => 4,
                'min_rbt12' => 720000.01,
                'max_rbt12' => 1800000,
                'aliquota_nominal' => 0.16,
                'deducao' => 35640,
                'irpj' => 0.04,
                'csll' => 0.035,
                'cofins' => 0.141,
                'pis' => 0.0305,
                'cpp' => 0.434,
                'iss' => 0.3195,
            ],
            [
                'faixa' => 5,
                'min_rbt12' => 1800000.01,
                'max_rbt12' => 3600000,
                'aliquota_nominal' => 0.21,
                'deducao' => 125640,
                'irpj' => 0.04,
                'csll' => 0.035,
                'cofins' => 0.1442,
                'pis' => 0.0313,
                'cpp' => 0.434,
                'iss' => 0.3155,
            ],
            [
                'faixa' => 6,
                'min_rbt12' => 3600000.01,
                'max_rbt12' => 4800000,
                'aliquota_nominal' => 0.33,
                'deducao' => 648000,
                'irpj' => 0.35,
                'csll' => 0.15,
                'cofins' => 0.1603,
                'pis' => 0.0347,
                'cpp' => 0.305,
                'iss' => 0,
            ],
        ];

        DB::table('tax_brackets')->insert($brackets);
    }
}
