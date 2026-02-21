<?php

namespace App\Services;

use Illuminate\Support\Collection;

class DasCalculatorService
{
    /**
     * Tabela do Anexo III — Alíquotas Nominais e Parcela a Deduzir
     * Fonte: Lei Complementar nº 155/2016
     */
    private const ALIQUOTA_TABLE = [
        ['faixa' => 1, 'min' => 0,          'max' => 180000,    'nominal' => 0.06,  'deducao' => 0],
        ['faixa' => 2, 'min' => 180000.01,  'max' => 360000,    'nominal' => 0.112, 'deducao' => 9360],
        ['faixa' => 3, 'min' => 360000.01,  'max' => 720000,    'nominal' => 0.135, 'deducao' => 17640],
        ['faixa' => 4, 'min' => 720000.01,  'max' => 1800000,   'nominal' => 0.16,  'deducao' => 35640],
        ['faixa' => 5, 'min' => 1800000.01, 'max' => 3600000,   'nominal' => 0.21,  'deducao' => 125640],
        ['faixa' => 6, 'min' => 3600000.01, 'max' => 4800000,   'nominal' => 0.33,  'deducao' => 648000],
    ];

    /**
     * Tabela do Anexo III — Percentual de Repartição dos Tributos por Faixa
     */
    private const TRIBUTOS_TABLE = [
        1 => ['irpj' => 0.04,  'csll' => 0.035, 'cofins' => 0.1282, 'pis' => 0.0278, 'cpp' => 0.434,  'iss' => 0.335],
        2 => ['irpj' => 0.04,  'csll' => 0.035, 'cofins' => 0.1405, 'pis' => 0.0305, 'cpp' => 0.434,  'iss' => 0.32],
        3 => ['irpj' => 0.04,  'csll' => 0.035, 'cofins' => 0.1364, 'pis' => 0.0296, 'cpp' => 0.434,  'iss' => 0.325],
        4 => ['irpj' => 0.04,  'csll' => 0.035, 'cofins' => 0.141,  'pis' => 0.0305, 'cpp' => 0.434,  'iss' => 0.3195],
        5 => ['irpj' => 0.04,  'csll' => 0.035, 'cofins' => 0.1442, 'pis' => 0.0313, 'cpp' => 0.434,  'iss' => 0.3155],
        6 => ['irpj' => 0.35,  'csll' => 0.15,  'cofins' => 0.1603, 'pis' => 0.0347, 'cpp' => 0.305,  'iss' => 0],
    ];

    /**
     * Calcula o RBT12: soma das receitas dos 12 meses anteriores ao PA.
     * Não inclui o mês atual (conforme legislação).
     *
     * @param int        $month    Mês do período de apuração (1-12)
     * @param int        $year     Ano do período de apuração
     * @param Collection $revenues Collection de Revenue models
     * @return array{rbt12: float, rbt12_data: array}
     */
    public function calcularRbt12(int $month, int $year, Collection $revenues): array
    {
        $rbt12Data = [];
        $rbt12     = 0.0;

        // Começa pelo mês anterior ao PA
        $prevMonth = $month - 1;
        $prevYear  = $year;

        if ($prevMonth === 0) {
            $prevMonth = 12;
            $prevYear--;
        }

        for ($i = 0; $i < 12; $i++) {
            $revenue = $revenues->first(
                fn($r) => $r->month === $prevMonth && $r->year === $prevYear
            );

            $amount = $revenue ? (float) $revenue->amount : 0.0;

            $rbt12Data[] = [
                'month'  => $prevMonth,
                'year'   => $prevYear,
                'amount' => $amount,
            ];

            $rbt12 += $amount;

            $prevMonth--;
            if ($prevMonth === 0) {
                $prevMonth = 12;
                $prevYear--;
            }
        }

        return ['rbt12' => $rbt12, 'rbt12_data' => $rbt12Data];
    }

    /**
     * Identifica a faixa tributária com base no RBT12.
     *
     * @param float $rbt12
     * @return array|null  Retorna null se exceder R$ 4.800.000
     */
    public function identificarFaixa(float $rbt12): ?array
    {
        foreach (self::ALIQUOTA_TABLE as $faixa) {
            if ($rbt12 <= $faixa['max']) {
                return $faixa;
            }
        }

        return null; // Acima do limite do Simples Nacional
    }

    /**
     * Calcula a alíquota efetiva.
     * Caso especial: quando RBT12 ≤ R$ 180.000, a alíquota efetiva = alíquota nominal (sem dedução).
     *
     * @return array{aliquota_efetiva: float, special_case: bool}
     */
    public function calcularAliquotaEfetiva(float $rbt12, array $faixa): array
    {
        $specialCase = ($rbt12 <= 180000);

        if ($specialCase) {
            $aliquotaEfetiva = $faixa['nominal'];
        } else {
            // Fórmula: ((RBT12 × Alíquota Nominal) − Parcela a Deduzir) ÷ RBT12
            $aliquotaEfetiva = (($rbt12 * $faixa['nominal']) - $faixa['deducao']) / $rbt12;
        }

        return ['aliquota_efetiva' => $aliquotaEfetiva, 'special_case' => $specialCase];
    }

    /**
     * Executa o cálculo completo do DAS para um período.
     *
     * @param int        $month    Mês do PA
     * @param int        $year     Ano do PA
     * @param float      $rpa      Receita Bruta do Período de Apuração
     * @param Collection $revenues Todas as receitas cadastradas
     * @return array                Dados completos para exibição e persistência
     *
     * @throws \RuntimeException Se RBT12 exceder o limite do Simples Nacional
     */
    public function calcular(int $month, int $year, float $rpa, Collection $revenues): array
    {
        ['rbt12' => $rbt12, 'rbt12_data' => $rbt12Data] = $this->calcularRbt12($month, $year, $revenues);

        $faixa = $this->identificarFaixa($rbt12);

        if ($faixa === null) {
            throw new \RuntimeException(
                'Receita Bruta em 12 meses excede o limite do Simples Nacional (R$ 4.800.000,00). '.
                'A empresa não pode optar pelo Simples Nacional.'
            );
        }

        ['aliquota_efetiva' => $aliquotaEfetiva, 'special_case' => $specialCase]
            = $this->calcularAliquotaEfetiva($rbt12, $faixa);

        $valorTotalDas = $rpa * $aliquotaEfetiva;

        $tributos = self::TRIBUTOS_TABLE[$faixa['faixa']];

        return [
            'month'            => $month,
            'year'             => $year,
            'rpa'              => $rpa,
            'rbt12'            => $rbt12,
            'rbt12_data'       => $rbt12Data,
            'tax_bracket'      => $faixa['faixa'],
            'aliquota_nominal' => $faixa['nominal'],
            'parcela_deduzir'  => $faixa['deducao'],
            'aliquota_efetiva' => $aliquotaEfetiva,
            'valor_total_das'  => $valorTotalDas,
            'special_case'     => $specialCase,
            // Repartição dos tributos
            'irpj_percent'     => $tributos['irpj'],
            'irpj_value'       => $valorTotalDas * $tributos['irpj'],
            'csll_percent'     => $tributos['csll'],
            'csll_value'       => $valorTotalDas * $tributos['csll'],
            'cofins_percent'   => $tributos['cofins'],
            'cofins_value'     => $valorTotalDas * $tributos['cofins'],
            'pis_percent'      => $tributos['pis'],
            'pis_value'        => $valorTotalDas * $tributos['pis'],
            'cpp_percent'      => $tributos['cpp'],
            'cpp_value'        => $valorTotalDas * $tributos['cpp'],
            'iss_percent'      => $tributos['iss'],
            'iss_value'        => $valorTotalDas * $tributos['iss'],
        ];
    }

    /** Expõe as tabelas para a view de referência tributária */
    public static function getAliquotaTable(): array
    {
        return self::ALIQUOTA_TABLE;
    }

    public static function getTributosTable(): array
    {
        return self::TRIBUTOS_TABLE;
    }
}
