<?php

namespace App\Livewire;

use App\Models\TaxBracket;
use App\Services\TaxBracketComparatorService;
use Livewire\Component;

class TaxTablesManager extends Component
{
    public $brackets = [];

    public $checkResult = null;

    public $checking = false;

    public $showConfirm = false;

    public $correctionSummary = [];

    public function mount()
    {
        $this->loadBrackets();
    }

    public function loadBrackets()
    {
        $this->brackets = TaxBracket::orderBy('faixa')->get()->toArray();
    }

    public function checkForUpdates()
    {
        $this->checking = true;

        $comparator = app(TaxBracketComparatorService::class);
        $this->checkResult = $comparator->checkForUpdates();

        $this->checking = false;
    }

    public function closeModal()
    {
        $this->checkResult = null;
        $this->showConfirm = false;
        $this->correctionSummary = [];
    }

    public function prepareCorrection()
    {
        if (empty($this->checkResult['differences'])) {
            return;
        }

        $comparator = app(TaxBracketComparatorService::class);
        $official = $comparator->getOfficialBrackets();

        $summary = [];
        foreach ($this->checkResult['differences'] as $diff) {
            if ($diff['field'] === 'missing') {
                continue;
            }
            $summary[] = [
                'faixa' => $diff['faixa'],
                'field' => $diff['field'],
                'current' => $diff['current_value'],
                'official' => $diff['official_value'],
            ];
        }

        $this->correctionSummary = $summary;
        $this->showConfirm = true;
    }

    public function confirmCorrection()
    {
        $comparator = app(TaxBracketComparatorService::class);
        $official = $comparator->getOfficialBrackets();

        foreach ($official as $data) {
            TaxBracket::where('faixa', $data['faixa'])->update([
                'min_rbt12' => $data['min_rbt12'],
                'max_rbt12' => $data['max_rbt12'],
                'aliquota_nominal' => $data['aliquota_nominal'],
                'deducao' => $data['deducao'],
                'irpj' => $data['irpj'],
                'csll' => $data['csll'],
                'cofins' => $data['cofins'],
                'pis' => $data['pis'],
                'cpp' => $data['cpp'],
                'iss' => $data['iss'],
            ]);
        }

        $this->loadBrackets();
        $this->closeModal();
        $this->dispatch('tax-brackets-updated');

        $this->dispatch('flash-message', [
            'type' => 'success',
            'message' => 'Tabelas atualizadas com valores oficiais!',
        ]);
    }

    public function cancelConfirm()
    {
        $this->showConfirm = false;
        $this->correctionSummary = [];
    }

    public function updateBracket($index, $field, $value)
    {
        if (! isset($this->brackets[$index])) {
            return;
        }

        $bracketData = $this->brackets[$index];
        $id = $bracketData['id'];

        $bracket = TaxBracket::findOrFail($id);

        $cleanValue = str_replace(',', '.', (string) $value);

        if (is_numeric($cleanValue)) {
            // Se o campo for um percentual, a UI nos enviou como inteiro/decimal (ex: 6 para 6%).
            // Dividimos por 100 para voltar à base decimal de cálculo.
            if (! in_array($field, ['faixa', 'deducao', 'min_rbt12', 'max_rbt12', 'id'])) {
                $cleanValue = (float) $cleanValue / 100;
            }

            $bracket->$field = $cleanValue;
            $bracket->save();
            $this->brackets[$index][$field] = $cleanValue;

            $this->dispatch('flash-message', [
                'type' => 'success',
                'message' => 'Faixa '.$bracket->faixa.' atualizada com sucesso!',
            ]);
        } else {
            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'Valor inválido inserido!',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tax-tables-manager');
    }
}
