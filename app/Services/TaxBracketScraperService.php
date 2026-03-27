<?php

namespace App\Services;

use App\Models\TaxBracketVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaxBracketScraperService
{
    private const CACHE_TTL = 86400; // 24 horas

    private const URLs = [
        'planalto' => 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm',
        'senacon' => 'https://www.gov.br/senacon/pt-br/normativa/lcp-123-2006',
        'receita' => 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/tributos/simples-nacional',
    ];

    private const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    ];

    public function fetchOfficialBrackets(): array
    {
        // Tenta cache primeiro
        $cached = Cache::get('tax_brackets_official');
        if ($cached) {
            return ['data' => $cached, 'source' => 'cache'];
        }

        // Tenta cada fonte
        foreach (self::URLs as $name => $url) {
            try {
                $result = $this->tryFetch($url, $name);
                if (! empty($result)) {
                    Cache::put('tax_brackets_official', $result, self::CACHE_TTL);

                    return ['data' => $result, 'source' => "site_{$name}"];
                }
            } catch (\Exception $e) {
                Log::warning("Falha ao buscar do {$name}: ".$e->getMessage());

                continue;
            }
        }

        // Fallback: usa dados locais
        return [
            'data' => $this->getFallbackBrackets(),
            'source' => 'fallback',
        ];
    }

    private function tryFetch(string $url, string $sourceName): array
    {
        $userAgent = self::USER_AGENTS[array_rand(self::USER_AGENTS)];

        $response = Http::timeout(30)
            ->retry(1, 1000)
            ->withOptions([
                'verify' => false,
                'curl' => [
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 3,
                ],
            ])
            ->withHeaders([
                'User-Agent' => $userAgent,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ])
            ->get($url);

        if (! $response->successful()) {
            return [];
        }

        $html = $response->body();

        // Detectar encoding
        if (stripos($html, 'charset=iso-8859-1') !== false || stripos($html, 'charset=ISO-8859-1') !== false) {
            $html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
        }

        return $this->parseHtml($html, $sourceName);
    }

    private function parseHtml(string $html, string $sourceName): array
    {
        $baseData = [];
        $reparticaoData = [];

        // Encontrar seção do Anexo III
        $anexoPos = $this->findAnexoIII($html);
        if ($anexoPos === false) {
            return [];
        }

        $section = substr($html, $anexoPos, 150000);

        if (preg_match_all('/<table[^>]*>(.*?)<\/table>/si', $section, $tables)) {
            foreach ($tables[1] as $tableHtml) {
                // Ignora tabelas tachadas/revogadas
                if (stripos($tableHtml, '<strike') !== false ||
                    stripos($tableHtml, '<del') !== false ||
                    stripos($tableHtml, 'revogada') !== false) {
                    continue;
                }

                // Tabela de Alíquotas
                if (stripos($tableHtml, 'Receita Bruta') !== false &&
                    stripos($tableHtml, 'Valor a Deduzir') !== false) {
                    $baseData = $this->parseBaseTable($tableHtml);
                }

                // Tabela de Repartição
                if (stripos($tableHtml, 'Reparti') !== false ||
                    stripos($tableHtml, 'percentual') !== false) {
                    $reparticaoData = $this->parseReparticaoTable($tableHtml);
                }
            }
        }

        // Merge dos dados
        $merged = [];
        foreach ($baseData as $faixa => $base) {
            if (isset($reparticaoData[$faixa])) {
                $merged[] = array_merge($base, $reparticaoData[$faixa]);
            }
        }

        return count($merged) >= 6 ? $merged : [];
    }

    private function findAnexoIII(string $html): int|false
    {
        // Busca por diferentes formatos de anchor
        $patterns = [
            'name="anexoiii."',
            'name="anexoiii"',
            'id="anexoiii."',
            'id="anexoiii"',
            'ANEXO III',
            'Anexo III',
        ];

        $lastPos = false;
        foreach ($patterns as $pattern) {
            $pos = strrpos($html, $pattern);
            if ($pos !== false && ($lastPos === false || $pos > $lastPos)) {
                $lastPos = $pos;
            }
        }

        return $lastPos;
    }

    private function parseBaseTable(string $tableHtml): array
    {
        $data = [];

        if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $tableHtml, $rows)) {
            foreach ($rows[1] as $rowContent) {
                if (preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $rowContent, $cells)) {
                    $cellTexts = array_map(fn ($c) => trim(strip_tags($c)), $cells[1]);

                    if (count($cellTexts) >= 4 && preg_match('/^\d/', $cellTexts[0])) {
                        $faixa = (int) preg_replace('/\D/', '', $cellTexts[0]);
                        if ($faixa > 0 && $faixa <= 6) {
                            $data[$faixa] = [
                                'faixa' => $faixa,
                                'min_rbt12' => $this->extractMin($cellTexts[1] ?? '', $faixa),
                                'max_rbt12' => $this->extractMax($cellTexts[1] ?? ''),
                                'aliquota_nominal' => $this->extractPercent($cellTexts[2] ?? '0'),
                                'deducao' => $this->extractValue($cellTexts[3] ?? '0'),
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function parseReparticaoTable(string $tableHtml): array
    {
        $data = [];

        if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $tableHtml, $rows)) {
            foreach ($rows[1] as $rowContent) {
                if (preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $rowContent, $cells)) {
                    $cellTexts = array_map(fn ($c) => trim(strip_tags($c)), $cells[1]);

                    if (count($cellTexts) >= 7 && preg_match('/^\d/', $cellTexts[0])) {
                        $faixa = (int) preg_replace('/\D/', '', $cellTexts[0]);
                        if ($faixa > 0 && $faixa <= 6 && ! isset($data[$faixa])) {
                            $data[$faixa] = [
                                'irpj' => $this->extractPercent($cellTexts[1] ?? '0'),
                                'csll' => $this->extractPercent($cellTexts[2] ?? '0'),
                                'cofins' => $this->extractPercent($cellTexts[3] ?? '0'),
                                'pis' => $this->extractPercent($cellTexts[4] ?? '0'),
                                'cpp' => $this->extractPercent($cellTexts[5] ?? '0'),
                                'iss' => $this->extractPercent($cellTexts[6] ?? '0'),
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function extractMin(string $text, int $faixa): float
    {
        if ($faixa === 1) {
            return 0;
        }

        if (preg_match_all('/[\d.]+/', $text, $matches)) {
            if (count($matches[0]) > 1) {
                return $this->extractValue($matches[0][0]);
            }
        }

        return match ($faixa) {
            2 => 180000.01,
            3 => 360000.01,
            4 => 720000.01,
            5 => 1800000.01,
            6 => 3600000.01,
            default => 0,
        };
    }

    private function extractMax(string $text): float
    {
        if (preg_match_all('/[\d.]+,?\d{0,2}/', $text, $matches)) {
            if (! empty($matches[0])) {
                return $this->extractValue(end($matches[0]));
            }
        }

        return $this->extractValue($text);
    }

    private function extractPercent(string $text): float
    {
        $text = str_replace(['%', ','], ['', '.'], trim($text));

        return (float) preg_replace('/[^\d.]/', '', $text) ?: 0;
    }

    private function extractValue(string $text): float
    {
        $text = preg_replace('/[^\d.,]/', '', $text);
        $text = str_replace(',', '.', $text);

        return (float) $text ?: 0;
    }

    public function getFallbackBrackets(): array
    {
        $latest = TaxBracketVersion::orderByDesc('version')->first();
        if ($latest) {
            return $latest->payload;
        }

        return json_decode(
            file_get_contents(database_path('seeders/data/tax_brackets_v1.json')),
            true
        ) ?: [];
    }
}
