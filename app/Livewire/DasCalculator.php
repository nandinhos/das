<?php

namespace App\Livewire;

use App\Models\Calculation;
use App\Models\Revenue;
use App\Services\DasCalculatorService;
use Livewire\Attributes\On;
use Livewire\Component;

class DasCalculator extends Component
{
    public int    $month;
    public int    $year;

    public ?array $result           = null;
    public string $errorMessage     = '';
    public bool   $calculationSaved = false;

    public function mount(): void
    {
        $this->month = (int) now()->format('n');
        $this->year  = (int) now()->format('Y');
    }

    public function calculate(DasCalculatorService $service): void
    {
        $this->errorMessage     = '';
        $this->calculationSaved = false;
        $this->result           = null;

        $revenue = Revenue::where('month', $this->month)
                          ->where('year', $this->year)
                          ->first();

        if (! $revenue) {
            $this->errorMessage = "Não há receita registrada para {$this->monthName($this->month)}/{$this->year}. Registre a receita primeiro na aba \"Receitas Mensais\".";
            return;
        }

        $allRevenues = Revenue::all();

        try {
            $this->result = $service->calcular(
                $this->month,
                $this->year,
                (float) $revenue->amount,
                $allRevenues
            );
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /** Carrega um cálculo salvo quando o usuário clica em "Ver" no histórico */
    #[On('view-calculation')]
    public function loadFromHistory(int $month, int $year): void
    {
        $calc = Calculation::where('month', $month)->where('year', $year)->first();

        if (! $calc) {
            return;
        }

        $this->month = $calc->month;
        $this->year  = $calc->year;
        $this->errorMessage     = '';
        $this->calculationSaved = true;

        $this->result = [
            'month'            => $calc->month,
            'year'             => $calc->year,
            'rpa'              => (float) $calc->rpa,
            'rbt12'            => (float) $calc->rbt12,
            'rbt12_data'       => $calc->rbt12_data,
            'tax_bracket'      => (int)   $calc->tax_bracket,
            'aliquota_nominal' => (float) $calc->aliquota_nominal,
            'parcela_deduzir'  => (float) $calc->parcela_deduzir,
            'aliquota_efetiva' => (float) $calc->aliquota_efetiva,
            'valor_total_das'  => (float) $calc->valor_total_das,
            'special_case'     => (bool)  $calc->special_case,
            'irpj_percent'     => (float) $calc->irpj_percent,
            'irpj_value'       => (float) $calc->irpj_value,
            'csll_percent'     => (float) $calc->csll_percent,
            'csll_value'       => (float) $calc->csll_value,
            'cofins_percent'   => (float) $calc->cofins_percent,
            'cofins_value'     => (float) $calc->cofins_value,
            'pis_percent'      => (float) $calc->pis_percent,
            'pis_value'        => (float) $calc->pis_value,
            'cpp_percent'      => (float) $calc->cpp_percent,
            'cpp_value'        => (float) $calc->cpp_value,
            'iss_percent'      => (float) $calc->iss_percent,
            'iss_value'        => (float) $calc->iss_value,
        ];
    }

    public function saveToHistory(): void
    {
        if (! $this->result) {
            return;
        }

        Calculation::updateOrCreate(
            ['month' => $this->result['month'], 'year' => $this->result['year']],
            $this->result
        );

        $this->calculationSaved = true;
        $this->dispatch('flash-message',
            message: "Cálculo de {$this->monthName($this->result['month'])}/{$this->result['year']} salvo no histórico!",
            type: 'success'
        );
    }

    private function monthName(int $month): string
    {
        return [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ][$month] ?? '';
    }

    public function render()
    {
        return view('livewire.das-calculator', [
            'years'  => range(now()->year - 2, now()->year + 2),
            'months' => [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
            ],
        ]);
    }
}
