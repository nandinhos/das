<?php

namespace Tests\Unit;

use App\Services\TaxBracketScraperService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TaxBracketScraperServiceTest extends TestCase
{
    public function test_it_reports_site_planalto_on_successful_scraping()
    {
        // Simulando HTML válido do Anexo III
        $html = '<table><tr><td>Faixa</td><td>Receita</td><td>Alíquota</td><td>Dedução</td></tr>' .
                '<tr><td>Anexo III - Serviços</td><td></td><td></td><td></td></tr>' .
                '<tr><td>1ª Faixa</td><td>Até 180.000,00</td><td>6,00%</td><td>0,00</td><td>4,00%</td><td>3,50%</td><td>12,82%</td><td>2,78%</td></tr></table>';

        Http::fake([
            'planalto.gov.br/*' => Http::response($html, 200),
        ]);

        $scraper = new TaxBracketScraperService();
        $result = $scraper->fetchOfficialBrackets();

        $this->assertEquals('site_planalto', $result['source']);
        $this->assertNotEmpty($result['data']);
        $this->assertEquals(1, $result['data'][0]['faixa']);
    }

    public function test_it_reports_fallback_on_http_error()
    {
        Http::fake([
            'planalto.gov.br/*' => Http::response('Error', 500),
        ]);

        $scraper = new TaxBracketScraperService();
        $result = $scraper->fetchOfficialBrackets();

        $this->assertEquals('fallback', $result['source']);
        $this->assertNotEmpty($result['data']);
        // Deve ser igual à constante OFFICIAL_BRACKETS
        $this->assertEquals(6, $result['data'][0]['aliquota_nominal']);
    }

    public function test_it_reports_fallback_on_empty_scraping_results()
    {
        Http::fake([
            // HTML que não contém as palavras chave do Anexo III
            'planalto.gov.br/*' => Http::response('<html><body>Página sem tabelas de tributos</body></html>', 200),
        ]);

        $scraper = new TaxBracketScraperService();
        $result = $scraper->fetchOfficialBrackets();

        $this->assertEquals('fallback', $result['source']);
    }
}
