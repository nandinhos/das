<?php

namespace App\Livewire;

use App\Services\TaxBracketComparatorService;
use App\Services\TaxBracketScraperService;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.diagnostic')]
class ScraperDiagnostic extends Component
{
    private const PLANALTO_URL = 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm';

    public bool $running = false;
    public bool $ran = false;

    public array $connectionTest = [];
    public array $scraped = [];
    public array $fallback = [];
    public bool $usedFallback = false;
    public array $comparisonResult = [];

    public static array $tributeFields = ['irpj', 'csll', 'cofins', 'pis', 'cpp', 'iss'];

    public function run(): void
    {
        $this->running = true;

        // 1. Teste HTTP direto
        $start = microtime(true);
        try {
            $response = Http::timeout(15)->get(self::PLANALTO_URL);
            $duration = round((microtime(true) - $start) * 1000);

            $this->connectionTest = [
                'success'     => $response->successful(),
                'status_code' => $response->status(),
                'duration_ms' => $duration,
                'url'         => self::PLANALTO_URL,
                'error'       => null,
            ];
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $start) * 1000);
            $this->connectionTest = [
                'success'     => false,
                'status_code' => null,
                'duration_ms' => $duration,
                'url'         => self::PLANALTO_URL,
                'error'       => $e->getMessage(),
            ];
        }

        // 2. Dados do scraper (web ou fallback)
        $scraper = app(TaxBracketScraperService::class);
        $result = $scraper->fetchOfficialBrackets();
        
        $this->scraped      = $result['data'];
        $this->usedFallback = ($result['source'] === 'fallback');
        $this->fallback     = $scraper->getOfficialBracketsFallback();

        // 4. Resultado do comparador
        $this->comparisonResult = app(TaxBracketComparatorService::class)->checkForUpdates();

        $this->running = false;
        $this->ran = true;
    }

    public function highlightCurlError(string $error): string
    {
        $error = e($error);

        $patterns = [
            // URLs → azul-ciano sublinhado
            '/(https?:\/\/[^\s)]+)/'
                => '<span class="diag-token-url">$1</span>',
            // Código hex de erro (ex: 0A000126)
            '/\b([0-9A-F]{8})\b/'
                => '<span class="diag-token-errcode">$1</span>',
            // Números isolados
            '/(?<![:\w\/])(\b\d+\b)(?![:\w\/])/'
                => '<span class="diag-token-number">$1</span>',
            // Palavras-chave de protocolo
            '/\b(cURL error|errno|OpenSSL|SSL|error)\b/'
                => '<span class="diag-token-keyword">$1</span>',
            // Identificadores com underscore (SSL_read, etc.)
            '/\b([A-Za-z][A-Za-z0-9]*_[A-Za-z_][A-Za-z0-9_]*)\b/'
                => '<span class="diag-token-method">$1</span>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $error = preg_replace($pattern, $replacement, $error);
        }

        return $error;
    }

    public function render()
    {
        return view('livewire.scraper-diagnostic');
    }
}
