<?php

namespace Tests\Unit;

use App\Services\TaxBracketScraperService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TaxBracketScraperServiceTest extends TestCase
{
    public function test_it_reports_site_planalto_on_successful_scraping_with_two_tables()
    {
        // Simulando as duas tabelas do Anexo III com as 6 faixas (exigido pelo parser)
        $html = '
            <!-- Tabela 1: Alíquotas -->
            <table>
                <tr><td colspan="2">ANEXO III - Receita Bruta</td><td>Alíquota</td><td>Valor a Deduzir</td></tr>
                <tr><td>1ª Faixa</td><td>Até 180.000,00</td><td>6,00%</td><td>–</td></tr>
                <tr><td>2ª Faixa</td><td>De 180.000,01 a 360.000,00</td><td>11,20%</td><td>9.360,00</td></tr>
                <tr><td>3ª Faixa</td><td>De 360.000,01 a 720.000,00</td><td>13,50%</td><td>17.640,00</td></tr>
                <tr><td>4ª Faixa</td><td>De 720.000,01 a 1.800.000,00</td><td>16,00%</td><td>35.640,00</td></tr>
                <tr><td>5ª Faixa</td><td>De 1.800.000,01 a 3.600.000,00</td><td>21,00%</td><td>125.640,00</td></tr>
                <tr><td>6ª Faixa</td><td>De 3.600.000,01 a 4.800.000,00</td><td>33,00%</td><td>648.000,00</td></tr>
            </table>
            
            <!-- Tabela 2: Repartição -->
            <table>
                <tr><td>ANEXO III - Repartição dos Tributos</td><td colspan="6">Detalhamento</td></tr>
                <tr><td>Faixa</td><td>IRPJ</td><td>CSLL</td><td>Cofins</td><td>PIS</td><td>CPP</td><td>ISS</td></tr>
                <tr><td>1ª Faixa</td><td>4,00%</td><td>3,50%</td><td>12,82%</td><td>2,78%</td><td>43,40%</td><td>33,50%</td></tr>
                <tr><td>2ª Faixa</td><td>4,00%</td><td>3,50%</td><td>14,05%</td><td>3,05%</td><td>43,40%</td><td>32,00%</td></tr>
                <tr><td>3ª Faixa</td><td>4,00%</td><td>3,50%</td><td>13,64%</td><td>2,96%</td><td>43,40%</td><td>32,50%</td></tr>
                <tr><td>4ª Faixa</td><td>4,00%</td><td>3,50%</td><td>14,10%</td><td>3,05%</td><td>43,40%</td><td>31,95%</td></tr>
                <tr><td>5ª Faixa</td><td>4,00%</td><td>3,50%</td><td>14,42%</td><td>3,13%</td><td>43,40%</td><td>31,55%</td></tr>
                <tr><td>6ª Faixa</td><td>35,00%</td><td>15,00%</td><td>16,03%</td><td>3,47%</td><td>30,50%</td><td>0,00%</td></tr>
            </table>';

        Http::fake([
            'planalto.gov.br/*' => Http::response($html, 200),
        ]);

        $scraper = new TaxBracketScraperService();
        $result = $scraper->fetchOfficialBrackets();

        $this->assertEquals('site_planalto', $result['source']);
        $this->assertCount(6, $result['data']);
        
        $faixa1 = $result['data'][0];
        $this->assertEquals(6, $faixa1['aliquota_nominal']);
        $this->assertEquals(4, $faixa1['irpj']);
        $this->assertEquals(43.4, $faixa1['cpp']);
        $this->assertEquals(33.5, $faixa1['iss']);
        $this->assertEquals(0, $faixa1['deducao']);

        $faixa6 = $result['data'][5];
        $this->assertEquals(33, $faixa6['aliquota_nominal']);
        $this->assertEquals(35, $faixa6['irpj']);
        $this->assertEquals(0, $faixa6['iss']);
    }

    public function test_it_reports_fallback_on_http_error()
    {
        Http::fake([
            'planalto.gov.br/*' => Http::response('Error', 500),
        ]);

        $scraper = new TaxBracketScraperService();
        $result = $scraper->fetchOfficialBrackets();

        $this->assertEquals('fallback', $result['source']);
    }

    public function test_it_reports_fallback_on_incomplete_data()
    {
        // Simulando apenas uma tabela
        $html = '
            <table>
                <tr><td colspan="2">ANEXO III - Receita Bruta</td><td>Alíquota</td><td>Valor a Deduzir</td></tr>
                <tr><td>1ª Faixa</td><td>Até 180.000,00</td><td>6,00%</td><td>–</td></tr>
            </table>';

        Http::fake([
            'planalto.gov.br/*' => Http::response($html, 200),
        ]);

        $scraper = new TaxBracketScraperService();
        $result = $scraper->fetchOfficialBrackets();

        $this->assertEquals('fallback', $result['source']);
    }
}
