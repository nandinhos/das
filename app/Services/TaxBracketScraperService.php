<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class TaxBracketScraperService
{
    private const PLANALTO_URL = 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm';

    private const OFFICIAL_BRACKETS = [
        [
            'faixa' => 1,
            'min_rbt12' => 0,
            'max_rbt12' => 180000,
            'aliquota_nominal' => 0.06,
            'deducao' => 0,
            'irpj' => 0.04,
            'csll' => 0.035,
            'cofins' => 0.1282,
            'pis' => 0.0278,
            'cpp' => 0.434,
            'iss' => 0.335,
        ],
        [
            'faixa' => 2,
            'min_rbt12' => 180000.01,
            'max_rbt12' => 360000,
            'aliquota_nominal' => 0.112,
            'deducao' => 9360,
            'irpj' => 0.04,
            'csll' => 0.035,
            'cofins' => 0.1405,
            'pis' => 0.0305,
            'cpp' => 0.434,
            'iss' => 0.32,
        ],
        [
            'faixa' => 3,
            'min_rbt12' => 360000.01,
            'max_rbt12' => 720000,
            'aliquota_nominal' => 0.135,
            'deducao' => 17640,
            'irpj' => 0.04,
            'csll' => 0.035,
            'cofins' => 0.1364,
            'pis' => 0.0296,
            'cpp' => 0.434,
            'iss' => 0.325,
        ],
        [
            'faixa' => 4,
            'min_rbt12' => 720000.01,
            'max_rbt12' => 1800000,
            'aliquota_nominal' => 0.16,
            'deducao' => 35640,
            'irpj' => 0.04,
            'csll' => 0.035,
            'cofins' => 0.141,
            'pis' => 0.0305,
            'cpp' => 0.434,
            'iss' => 0.3195,
        ],
        [
            'faixa' => 5,
            'min_rbt12' => 1800000.01,
            'max_rbt12' => 3600000,
            'aliquota_nominal' => 0.21,
            'deducao' => 125640,
            'irpj' => 0.04,
            'csll' => 0.035,
            'cofins' => 0.1442,
            'pis' => 0.0313,
            'cpp' => 0.434,
            'iss' => 0.3155,
        ],
        [
            'faixa' => 6,
            'min_rbt12' => 3600000.01,
            'max_rbt12' => 4800000,
            'aliquota_nominal' => 0.33,
            'deducao' => 648000,
            'irpj' => 0.35,
            'csll' => 0.15,
            'cofins' => 0.1603,
            'pis' => 0.0347,
            'cpp' => 0.305,
            'iss' => 0,
        ],
    ];

    public function fetchOfficialBrackets(): array
    {
        try {
            $response = Http::timeout(30)->get(self::PLANALTO_URL);

            if (! $response->successful()) {
                Log::warning('Failed to fetch tax brackets from Planalto', [
                    'status' => $response->status(),
                ]);

                return $this->getOfficialBracketsFallback();
            }

            return $this->parseHtml($response->body());
        } catch (\Exception $e) {
            Log::error('Error fetching tax brackets', [
                'error' => $e->getMessage(),
            ]);

            return $this->getOfficialBracketsFallback();
        }
    }

    private function parseHtml(string $html): array
    {
        $crawler = new Crawler($html);
        $brackets = [];

        $crawler->filter('table')->each(function (Crawler $table) use (&$brackets) {
            $text = $table->text();

            if (stripos($text, 'Anexo III') !== false && stripos($text, 'Serviços') !== false) {
                $table->filter('tr')->each(function (Crawler $row, $index) use (&$brackets) {
                    if ($index === 0) {
                        return;
                    }

                    $cells = $row->filter('td');
                    if ($cells->count() >= 8) {
                        $brackets[] = $this->parseRow($cells);
                    }
                });
            }
        });

        if (empty($brackets)) {
            return $this->getOfficialBracketsFallback();
        }

        return $brackets;
    }

    private function parseRow(Crawler $cells): array
    {
        $texts = $cells->each(fn (Crawler $cell) => trim($cell->text()));

        return [
            'faixa' => $this->extractFaixa($texts[0] ?? ''),
            'min_rbt12' => $this->extractValue($texts[1] ?? '0'),
            'max_rbt12' => $this->extractValue($texts[2] ?? '0'),
            'aliquota_nominal' => $this->extractPercentage($texts[3] ?? '0'),
            'deducao' => $this->extractValue($texts[4] ?? '0'),
        ];
    }

    private function extractFaixa(string $text): int
    {
        preg_match('/\d+/', $text, $matches);

        return (int) ($matches[0] ?? 1);
    }

    private function extractValue(string $text): float
    {
        $text = preg_replace('/[^\d,.]/', '', $text);
        $text = str_replace('.', '', $text);
        $text = str_replace(',', '.', $text);

        return (float) $text;
    }

    private function extractPercentage(string $text): float
    {
        $text = str_replace('%', '', trim($text));
        $text = str_replace(',', '.', $text);

        return (float) $text / 100;
    }

    public function getOfficialBracketsFallback(): array
    {
        return self::OFFICIAL_BRACKETS;
    }

    public function getOfficialBrackets(): array
    {
        return self::OFFICIAL_BRACKETS;
    }
}
