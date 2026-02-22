<?php

namespace App\Livewire;

use App\Models\TaxBracket;
use Livewire\Component;

class TaxTablesManager extends Component
{
    public $brackets = [];

    public function mount()
    {
        $this->loadBrackets();
    }

    public function loadBrackets()
    {
        $this->brackets = TaxBracket::orderBy('faixa')->get()->toArray();
    }

    public function updateBracket($index, $field, $value)
    {
        if (!isset($this->brackets[$index])) {
            return;
        }

        $bracketData = $this->brackets[$index];
        $id = $bracketData['id'];

        $bracket = TaxBracket::findOrFail($id);
        
        $cleanValue = str_replace(',', '.', (string) $value);

        if (is_numeric($cleanValue)) {
            // Se o campo for um percentual, a UI nos enviou como inteiro/decimal (ex: 6 para 6%).
            // Dividimos por 100 para voltar à base decimal de cálculo.
            if (!in_array($field, ['faixa', 'deducao', 'min_rbt12', 'max_rbt12', 'id'])) {
                $cleanValue = (float) $cleanValue / 100;
            }

            $bracket->$field = $cleanValue;
            $bracket->save();
            $this->brackets[$index][$field] = $cleanValue;

            $this->dispatch('flash-message', [
                'type' => 'success',
                'message' => 'Faixa ' . $bracket->faixa . ' atualizada com sucesso!'
            ]);
        } else {
            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'Valor inválido inserido!'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tax-tables-manager');
    }
}
