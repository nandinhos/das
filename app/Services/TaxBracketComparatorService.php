<?php

namespace App\Services;

use App\Models\TaxBracket;
use Illuminate\Support\Collection;

class TaxBracketComparatorService
{
    private TaxBracketScraperService $scraper;

    private const COMPARABLE_FIELDS = [
        'min_rbt12',
        'max_rbt12',
        'aliquota_nominal',
        'deducao',
        'irpj',
        'csll',
        'cofins',
        'pis',
        'cpp',
        'iss',
    ];

    public function __construct(TaxBracketScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function checkForUpdates(): array
    {
        $result = $this->scraper->fetchOfficialBrackets();
        $officialBrackets = $result['data'];
        $source = $result['source'];

        $localBrackets = TaxBracket::orderBy('faixa')->get();

        if (empty($officialBrackets)) {
            return [
                'status'      => 'error',
                'checked_at'  => now()->toIso8601String(),
                'source'      => $source,
                'message'     => 'Failed to fetch official brackets or empty data',
                'differences' => [],
            ];
        }

        // Comparação rápida por checksum — evita comparação campo a campo se já sincronizado
        $localChecksum    = static::computeChecksum($localBrackets->toArray());
        $officialChecksum = static::computeChecksum($officialBrackets);

        if ($localChecksum === $officialChecksum) {
            return [
                'status'      => 'uptodate',
                'checked_at'  => now()->toIso8601String(),
                'source'      => $source,
                'differences' => [],
            ];
        }

        $differences = $this->compare($localBrackets, collect($officialBrackets));

        return [
            'status'      => empty($differences) ? 'uptodate' : 'outdated',
            'checked_at'  => now()->toIso8601String(),
            'source'      => $source,
            'differences' => $differences,
        ];
    }

    public static function computeChecksum(array $brackets): string
    {
        $normalized = collect($brackets)
            ->sortBy('faixa')
            ->map(fn ($b) => collect($b)
                ->only(self::COMPARABLE_FIELDS)
                ->map(fn ($v) => (float) $v)
                ->sortKeys()
                ->toArray()
            )
            ->values()
            ->toArray();

        return hash('sha256', json_encode($normalized));
    }

    public function compare(Collection $localBrackets, Collection $officialBrackets): array
    {
        $differences = [];

        foreach ($localBrackets as $local) {
            $official = $officialBrackets->firstWhere('faixa', $local->faixa);

            if (! $official) {
                $differences[] = [
                    'faixa'          => $local->faixa,
                    'field'          => 'missing',
                    'current_value'  => null,
                    'official_value' => null,
                    'difference'     => 'Faixa existe localmente mas não encontrada na fonte oficial',
                ];

                continue;
            }

            foreach (self::COMPARABLE_FIELDS as $field) {
                $localValue    = (float) $local->$field;
                $officialValue = (float) $official[$field];

                if (! $this->valuesAreEqual($localValue, $officialValue)) {
                    $differences[] = [
                        'faixa'          => $local->faixa,
                        'field'          => $field,
                        'current_value'  => $localValue,
                        'official_value' => $officialValue,
                        'difference'     => $localValue - $officialValue,
                    ];
                }
            }
        }

        foreach ($officialBrackets as $official) {
            $local = $localBrackets->firstWhere('faixa', $official['faixa']);
            if (! $local) {
                $differences[] = [
                    'faixa'          => $official['faixa'],
                    'field'          => 'missing',
                    'current_value'  => null,
                    'official_value' => 'Nova faixa na fonte oficial',
                    'difference'     => 'Nova faixa disponível na legislação',
                ];
            }
        }

        return $differences;
    }

    private function valuesAreEqual(float $value1, float $value2): bool
    {
        $epsilon = 0.0001;

        return abs($value1 - $value2) < $epsilon;
    }

    public function getLocalBrackets(): Collection
    {
        return TaxBracket::orderBy('faixa')->get();
    }

    public function getOfficialBrackets(): array
    {
        return $this->scraper->getFallbackBrackets();
    }
}
