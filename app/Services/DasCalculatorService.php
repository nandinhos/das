<?php

namespace App\Services;

use App\Models\TaxBracket;
use Illuminate\Support\Collection;

class DasCalculatorService
{
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
        foreach (self::getAliquotaTable() as $faixa) {
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

        $tributos = self::getTributosTable()[$faixa['faixa']];

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

    /** 
     * Expõe as tabelas para a view de referência tributária e cálculo, 
     * agora buscando os dados do Banco de Dados usando App\Models\TaxBracket.
     */
    public static function getAliquotaTable(): array
    {
        return TaxBracket::orderBy('faixa')->get()->map(function ($bracket) {
            return [
                'faixa' => $bracket->faixa,
                'min' => (float) $bracket->min_rbt12,
                'max' => (float) $bracket->max_rbt12,
                'nominal' => (float) $bracket->aliquota_nominal,
                'deducao' => (float) $bracket->deducao,
            ];
        })->toArray();
    }

    public static function getTributosTable(): array
    {
        return TaxBracket::orderBy('faixa')->get()->keyBy('faixa')->map(function ($bracket) {
            return [
                'irpj' => (float) $bracket->irpj,
                'csll' => (float) $bracket->csll,
                'cofins' => (float) $bracket->cofins,
                'pis' => (float) $bracket->pis,
                'cpp' => (float) $bracket->cpp,
                'iss' => (float) $bracket->iss,
            ];
        })->toArray();
    }
}
