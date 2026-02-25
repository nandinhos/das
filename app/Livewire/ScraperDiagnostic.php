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
    public array $scraperMeta = [];
    public array $scraped = [];
    public array $fallback = [];
    public bool $usedFallback = false;
    public array $comparisonResult = [];

    public function run(): void
    {
        $this->running = true;

        // 1. Teste HTTP direto
        $start = microtime(true);
        try {
            $response = Http::timeout(15)
                ->withOptions([
                    'verify' => false,
                    'curl'   => [CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1],
                ])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get(self::PLANALTO_URL);
            $duration = round((microtime(true) - $start) * 1000);

            $this->connectionTest = [
                'success'     => $response->successful(),
                'status_code' => $response->status(),
                'duration_ms' => $duration,
                'url'         => self::PLANALTO_URL,
                'error'       => null,
                'html_size'   => strlen($response->body()),
            ];
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $start) * 1000);
            $this->connectionTest = [
                'success'     => false,
                'status_code' => null,
                'duration_ms' => $duration,
                'url'         => self::PLANALTO_URL,
                'error'       => $e->getMessage(),
                'html_size'   => 0,
            ];
        }

        // 2. Dados do scraper (web ou fallback)
        $scraperStart = microtime(true);
        $scraper = app(TaxBracketScraperService::class);
        $result = $scraper->fetchOfficialBrackets();
        $scraperDuration = round((microtime(true) - $scraperStart) * 1000);

        $this->scraped      = $result['data'];
        $this->usedFallback = ($result['source'] === 'fallback');
        $this->fallback     = $scraper->getOfficialBracketsFallback();

        // Metadados do scraping
        $this->scraperMeta = [
            'source'        => $result['source'],
            'duration_ms'   => $scraperDuration,
            'faixas'        => count($result['data']),
            'campos'        => !empty($result['data']) ? count($result['data'][0]) : 0,
            'parser'        => 'Regex (preg_match_all)',
            'encoding'      => 'ISO-8859-1 → UTF-8',
            'checked_at'    => now()->format('d/m/Y H:i:s'),
        ];

        // 3. Resultado do comparador
        $this->comparisonResult = app(TaxBracketComparatorService::class)->checkForUpdates();

        $this->running = false;
        $this->ran = true;
    }

    public function render()
    {
        return view('livewire.scraper-diagnostic');
    }
}
