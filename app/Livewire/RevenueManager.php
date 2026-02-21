<?php

namespace App\Livewire;

use App\Models\Revenue;
use Livewire\Component;

class RevenueManager extends Component
{
    public int    $month;
    public int    $year;
    public string $amount = '';

    public ?int   $editingId      = null;
    public bool   $showDeleteModal = false;
    public ?int   $deleteId        = null;
    public string $deleteMessage   = '';

    protected function rules(): array
    {
        return [
            'month'  => 'required|integer|min:1|max:12',
            'year'   => 'required|integer|min:2020|max:2035',
            'amount' => 'required|string|min:1',
        ];
    }

    public function mount(): void
    {
        $this->month = (int) now()->format('n');
        $this->year  = (int) now()->format('Y');
    }

    public function save(): void
    {
        $this->validate();

        $amountFloat = $this->parseCurrency($this->amount);

        if ($amountFloat <= 0) {
            $this->addError('amount', 'Insira um valor válido para a receita.');
            return;
        }

        if ($amountFloat > 4800000) {
            $this->addError('amount', 'Valor não pode exceder R$ 4.800.000,00 (limite do Simples Nacional).');
            return;
        }

        if ($this->editingId) {
            $revenue = Revenue::findOrFail($this->editingId);
            $revenue->update([
                'month'  => $this->month,
                'year'   => $this->year,
                'amount' => $amountFloat,
            ]);
            $this->dispatch('flash-message',
                message: "Receita de {$this->monthName($this->month)}/{$this->year} atualizada com sucesso!",
                type: 'success'
            );
            $this->editingId = null;
        } else {
            Revenue::updateOrCreate(
                ['month' => $this->month, 'year' => $this->year],
                ['amount' => $amountFloat]
            );
            $this->dispatch('flash-message',
                message: "Receita de {$this->monthName($this->month)}/{$this->year} salva com sucesso!",
                type: 'success'
            );
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $revenue     = Revenue::findOrFail($id);
        $this->editingId = $id;
        $this->month     = $revenue->month;
        $this->year      = $revenue->year;
        $this->amount    = number_format((float) $revenue->amount, 2, ',', '.');
        $this->resetErrorBag();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $revenue             = Revenue::findOrFail($id);
        $this->deleteId      = $id;
        $this->deleteMessage = "Tem certeza que deseja excluir a receita de {$this->monthName($revenue->month)}/{$revenue->year}?";
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $revenue = Revenue::findOrFail($this->deleteId);
            $name    = "{$this->monthName($revenue->month)}/{$revenue->year}";
            $revenue->delete();
            $this->dispatch('flash-message',
                message: "Receita de {$name} excluída com sucesso!",
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

    private function resetForm(): void
    {
        $this->amount = '';
        $this->month  = (int) now()->format('n');
        $this->year   = (int) now()->format('Y');
        $this->resetErrorBag();
    }

    private function parseCurrency(string $value): float
    {
        $clean = str_replace(['.', ' ', 'R$', "\u{00A0}"], '', $value);
        $clean = str_replace(',', '.', $clean);
        return (float) preg_replace('/[^\d.]/', '', $clean);
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
        return view('livewire.revenue-manager', [
            'revenues' => Revenue::ordered()->get(),
            'years'    => range(now()->year - 2, now()->year + 2),
            'months'   => [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
            ],
        ]);
    }
}
