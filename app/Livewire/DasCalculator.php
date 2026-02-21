<?php

namespace App\Livewire;

use App\Models\Revenue;
use App\Models\Calculation;
use App\Services\DasCalculatorService;
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
