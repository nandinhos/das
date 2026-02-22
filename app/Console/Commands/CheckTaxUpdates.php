<?php

namespace App\Console\Commands;

use App\Services\TaxBracketComparatorService;
use Illuminate\Console\Command;

class CheckTaxUpdates extends Command
{
    protected $signature = 'tax:check-updates {--detailed : Output detailed information}';

    protected $description = 'Check for updates in tax brackets against official sources';

    public function __construct(
        private TaxBracketComparatorService $comparator
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Verificando tabelas tributárias...');

        $result = $this->comparator->checkForUpdates();

        if ($result['status'] === 'error') {
            $this->error('Erro ao verificar tabelas: '.($result['message'] ?? 'Erro desconhecido'));

            return Command::FAILURE;
        }

        if ($result['status'] === 'uptodate') {
            $this->info('✓ Tabelas estão atualizadas!');
            $this->line('Última verificação: '.$result['checked_at']);

            return Command::SUCCESS;
        }

        $this->warn('⚠ Diferenças encontradas nas tabelas!');
        $this->line('Última verificação: '.$result['checked_at']);
        $this->newLine();

        if ($this->option('detailed')) {
            $this->table(
                ['Faixa', 'Campo', 'Valor Atual', 'Valor Oficial', 'Diferença'],
                array_map(function ($diff) {
                    return [
                        $diff['faixa'].'ª',
                        $diff['field'],
                        number_format($diff['current_value'] ?? 0, 4, ',', '.'),
                        number_format($diff['official_value'] ?? 0, 4, ',', '.'),
                        number_format($diff['difference'] ?? 0, 4, ',', '.'),
                    ];
                }, $result['differences'])
            );
        } else {
            $this->line('Execute com --verbose para ver os detalhes.');
        }

        return Command::SUCCESS;
    }
}
