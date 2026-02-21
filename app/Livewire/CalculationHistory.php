<?php

namespace App\Livewire;

use App\Models\Calculation;
use Livewire\Attributes\On;
use Livewire\Component;

class CalculationHistory extends Component
{
    public bool   $showDeleteModal = false;
    public ?int   $deleteId        = null;
    public string $deleteMessage   = '';

    #[On('calculation-saved')]
    public function refresh(): void {}

    public function view(int $id): void
    {
        $calc = Calculation::findOrFail($id);
        // Navega para aba Calcular e exibe o resultado histórico
        $this->dispatch('view-calculation', month: $calc->month, year: $calc->year);
    }

    public function confirmDelete(int $id): void
    {
        $calc                = Calculation::findOrFail($id);
        $this->deleteId      = $id;
        $this->deleteMessage = "Tem certeza que deseja excluir o cálculo de {$this->monthName($calc->month)}/{$calc->year}?";
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $calc = Calculation::findOrFail($this->deleteId);
            $name = "{$this->monthName($calc->month)}/{$calc->year}";
            $calc->delete();
            $this->dispatch('flash-message',
                message: "Cálculo de {$name} excluído do histórico!",
                type: 'success'
            );
        }
        $this->showDeleteModal = false;
        $this->deleteId        = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId        = null;
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
        return view('livewire.calculation-history', [
            'calculations' => Calculation::ordered()->get(),
        ]);
    }
}
