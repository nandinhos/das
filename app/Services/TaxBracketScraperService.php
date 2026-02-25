<?php

namespace App\Services;

use App\Models\TaxBracketVersion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaxBracketScraperService
{
    private const PLANALTO_URL = 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm';

    public function fetchOfficialBrackets(): array
    {
        try {
            $response = Http::timeout(90)
                ->retry(2, 500)
                ->withOptions([
                    'verify' => false,
                    'curl'   => [CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1],
                ])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get(self::PLANALTO_URL);

            if (! $response->successful()) {
                Log::warning('Failed to fetch tax brackets from Planalto', [
                    'status' => $response->status(),
                ]);

                return [
                    'data'   => $this->getFallbackBrackets(),
                    'source' => 'fallback',
                ];
            }

            // O site do Planalto usa ISO-8859-1 — converter para UTF-8 antes de qualquer busca textual
            $fullHtml = mb_convert_encoding($response->body(), 'UTF-8', 'ISO-8859-1');

            // O texto "ANEXO III" está quebrado em linhas no HTML do Planalto — usar o anchor HTML.
            // A versão vigente usa name="anexoiii." (com ponto); a versão revogada usa name="anexoiii".
            $lastAnexoPos = strrpos($fullHtml, 'name="anexoiii."');
            if ($lastAnexoPos === false) {
                $lastAnexoPos = strrpos($fullHtml, 'name="anexoiii"');
            }

            $section = $fullHtml;
            if ($lastAnexoPos !== false) {
                // 100kb são suficientes para cobrir as duas tabelas do Anexo III
                $section = substr($fullHtml, $lastAnexoPos, 100000);
            }

            $scraped = $this->parseHtml($section);

            if (empty($scraped)) {
                return [
                    'data'   => $this->getFallbackBrackets(),
                    'source' => 'fallback',
                ];
            }

            return [
                'data'   => $scraped,
                'source' => 'site_planalto',
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching tax brackets', [
                'error' => $e->getMessage(),
            ]);

            return [
                'data'   => $this->getFallbackBrackets(),
                'source' => 'fallback',
            ];
        }
    }

    public function getFallbackBrackets(): array
    {
        // Tenta última versão salva no banco
        $latest = TaxBracketVersion::orderByDesc('version')->first();
        if ($latest) {
            return $latest->payload;
        }

        // Fallback absoluto: JSON do seeder
        return json_decode(
            file_get_contents(database_path('seeders/data/tax_brackets_v1.json')),
            true
        );
    }

    private function parseHtml(string $html): array
    {
        $baseData = [];
        $reparticaoData = [];

        // Encontra todas as tabelas
        if (preg_match_all('/<table.*?>(.*?)<\/table>/si', $html, $tables)) {
            foreach ($tables[1] as $tableHtml) {
                // Ignora tabelas tachadas
                if (stripos($tableHtml, '<strike') !== false || stripos($tableHtml, '<del') !== false) {
                    continue;
                }

                // Tabela 1: Alíquotas e Valores a Deduzir
                if (stripos($tableHtml, 'Receita Bruta') !== false && stripos($tableHtml, 'Valor a Deduzir') !== false) {
                    if (preg_match_all('/<tr.*?>(.*?)<\/tr>/si', $tableHtml, $rows)) {
                        foreach ($rows[1] as $rowContent) {
                            if (preg_match_all('/<td.*?>(.*?)<\/td>/si', $rowContent, $cells)) {
                                $cellTexts = array_map(fn($c) => trim(strip_tags($c)), $cells[1]);
                                if (count($cellTexts) >= 4 && preg_match('/^\d/u', $cellTexts[0])) {
                                    $data = $this->parseBaseRow($cellTexts);
                                    if ($data) {
                                        $baseData[$data['faixa']] = $data;
                                    }
                                }
                            }
                        }
                    }
                }

                // Tabela 2: Percentual de Repartição
                // Busca por 'Reparti' — texto quebrado no HTML, não usar a frase completa
                if (stripos($tableHtml, 'Reparti') !== false) {
                    if (preg_match_all('/<tr.*?>(.*?)<\/tr>/si', $tableHtml, $rows)) {
                        foreach ($rows[1] as $rowContent) {
                            if (preg_match_all('/<td.*?>(.*?)<\/td>/si', $rowContent, $cells)) {
                                $cellTexts = array_map(fn($c) => trim(strip_tags($c)), $cells[1]);
                                if (count($cellTexts) >= 7 && preg_match('/^\d/u', $cellTexts[0])) {
                                    $data = $this->parseReparticaoRow($cellTexts);
                                    // Preservar apenas o primeiro registro — a segunda ocorrência
                                    // de "5ª Faixa" no HTML contém fórmulas (alíquota efetiva > 14,93%)
                                    // que sobrescrevem os valores corretos com zeros.
                                    if ($data && ! isset($reparticaoData[$data['faixa']])) {
                                        $reparticaoData[$data['faixa']] = $data;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Mesclar dados
        $merged = [];
        foreach ($baseData as $faixa => $base) {
            if (isset($reparticaoData[$faixa])) {
                $merged[] = array_merge($base, $reparticaoData[$faixa]);
            }
        }

        return count($merged) === 6 ? $merged : [];
    }

    private function parseBaseRow(array $texts): ?array
    {
        $faixa = $this->extractFaixa($texts[0]);
        if ($faixa === 0) return null;

        return [
            'faixa'            => $faixa,
            'min_rbt12'        => $this->extractLimit($texts[1], $faixa),
            'max_rbt12'        => $this->extractLastValue($texts[1]),
            'aliquota_nominal' => $this->extractPercentage($texts[2]),
            'deducao'          => $this->extractValue($texts[3]),
        ];
    }

    private function parseReparticaoRow(array $texts): ?array
    {
        $faixa = $this->extractFaixa($texts[0]);
        if ($faixa === 0) return null;

        return [
            'faixa'  => $faixa,
            'irpj'   => $this->extractPercentage($texts[1]),
            'csll'   => $this->extractPercentage($texts[2]),
            'cofins' => $this->extractPercentage($texts[3]),
            'pis'    => $this->extractPercentage($texts[4]),
            'cpp'    => $this->extractPercentage($texts[5]),
            'iss'    => $this->extractPercentage($texts[6]),
        ];
    }

    private function extractFaixa(string $text): int
    {
        if (preg_match('/(\d+)/', $text, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }

    private function extractLimit(string $text, int $faixa): float
    {
        // Se for a primeira faixa, o mínimo é 0
        if ($faixa === 1) return 0;

        // Para as outras faixas, o mínimo é o limite inferior da faixa anterior + 0.01
        // Mas o site costuma mostrar "De X a Y", então podemos tentar extrair o primeiro valor se houver dois
        if (preg_match_all('/[\d.,]+/', $text, $matches)) {
            if (count($matches[0]) > 1) {
                return $this->extractValue($matches[0][0]);
            }
        }
        return 0; // Fallback simples
    }

    private function extractLastValue(string $text): float
    {
        // Extrai o último número de um range como "De 180.000,01 a 360.000,00" → 360000.00
        if (preg_match_all('/[\d.]+,\d{2}/', $text, $matches) && ! empty($matches[0])) {
            return $this->extractValue(end($matches[0]));
        }

        return $this->extractValue($text);
    }

    private function extractValue(string $text): float
    {
        // Trata o traço "–" ou palavras como "Até"
        if ($text === '–' || stripos($text, 'isento') !== false) {
            return 0.0;
        }

        $text = preg_replace('/[^\d,]/', '', $text);
        $text = str_replace(',', '.', $text);

        return (float) $text;
    }

    private function extractPercentage(string $text): float
    {
        $text = str_replace('%', '', trim($text));
        $text = str_replace(',', '.', $text);

        return (float) $text;
    }
}
