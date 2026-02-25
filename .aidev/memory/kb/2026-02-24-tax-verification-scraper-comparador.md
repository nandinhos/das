# Lição: Sistema de Verificação de Tabelas Tributárias (Scraper + Comparador)

**Data**: 2026-02-24
**Stack**: Laravel 11 + Livewire 4 + Http Client
**Tags**: scraping, legislação, simples-nacional, comparador, arquitetura, fallback
**Severidade**: Média — lições de design para scraping de fontes oficiais

---

## Contexto

Sistema para verificar se as tabelas do Simples Nacional (Anexo III) estão atualizadas
comparando os dados locais com a fonte oficial (LC 123/2006 no site do Planalto).

**Serviços criados:**
- `TaxBracketScraperService` — extrai tabelas do site do governo
- `TaxBracketComparatorService` — compara dados do banco com dados oficiais
- Interface Livewire com modal de resultado

---

## Arquitetura Implementada

```
Usuário clica "Verificar Tabelas"
    │
    ▼
TaxTablesManager (Livewire) → chama checkForUpdates()
    │
    ├─ TaxBracketScraperService::scrape()
    │    ├─ Http::get(planalto.gov.br)  ← tenta fonte online
    │    └─ fallback: OFFICIAL_BRACKETS ← constante hardcoded com dados vigentes
    │
    ├─ TaxBracketComparatorService::compare($scraped, $local)
    │    └─ Compara campo a campo com epsilon=0.01
    │
    └─ Retorna: status + diferenças + timestamp
         │
         ▼
    Modal com resultado visual
```

---

## Lições Aprendidas

### 1. Sites governamentais precisam de fallback hardcoded

O site do Planalto (planalto.gov.br) pode estar fora, mudar estrutura HTML ou ter latência alta.
**Padrão adotado:** constante `OFFICIAL_BRACKETS` no próprio scraper com os dados vigentes.

```php
// Em TaxBracketScraperService.php
const OFFICIAL_BRACKETS = [
    ['faixa' => 1, 'aliquota_nominal' => 6.0, 'deducao' => 0, ...],
    // ...
];

public function scrape(): array
{
    try {
        return $this->scrapeFromWeb();
    } catch (\Exception $e) {
        Log::warning('Scraper falhou, usando OFFICIAL_BRACKETS', ['error' => $e->getMessage()]);
        return self::OFFICIAL_BRACKETS;
    }
}
```

**Benefício:** O sistema nunca retorna erro ao usuário — sempre tem uma referência válida.

### 2. Epsilon de 0.01 para comparação de percentuais tributários

Floating point é impreciso. `6.0 == 6.000000001` falha na comparação direta.
Scraping de HTML pode introduzir variações mínimas (ex: `6.00` vs `6.0`).

```php
// TaxBracketComparatorService.php
private float $epsilon = 0.01;

private function valuesMatch(float $a, float $b): bool
{
    return abs($a - $b) <= $this->epsilon;
}
```

**Cuidado:** Epsilon muito pequeno (`0.0001`) causa falsos positivos de "desatualizado".
Para tabelas tributárias, diferenças < 0.01% são irrelevantes.

### 3. Formato dos dados do scraper deve ser igual ao do banco

O scraper deve retornar dados no **mesmo formato que o banco armazena**.
No nosso caso: percentuais brutos (ex: `6`, não `0.06`).

```php
// CORRETO: retorna percentual (igual ao banco)
private function extractPercentage(string $text): float
{
    return (float) preg_replace('/[^0-9,.]/', '', str_replace(',', '.', $text));
}

// ERRADO: retornava decimal, incompatível com banco
// return $extracted / 100;
```

**Regra:** O comparador só compara — não converte. O scraper entrega no formato do banco.

### 4. Separação de responsabilidades: Scraper ≠ Comparador

**Scraper**: Sabe como obter dados externos. Ignora o banco.
**Comparador**: Sabe comparar dois arrays. Ignora onde os dados vêm.

Isso permite:
- Testar o comparador com dados mockados (sem HTTP)
- Substituir o scraper sem alterar a lógica de comparação
- Reutilizar o comparador para outras fontes no futuro

### 5. Livewire + Modal para resultados assíncronos

O padrão de "verificar e abrir modal" funciona bem em Livewire:

```php
// TaxTablesManager.php
public bool $showUpdateModal = false;
public array $comparisonResult = [];

public function checkForUpdates(): void
{
    $scraped = $this->scraperService->scrape();
    $local = TaxBracket::all()->toArray();
    $this->comparisonResult = $this->comparatorService->compare($scraped, $local);
    $this->showUpdateModal = true;
}
```

```blade
{{-- blade --}}
<button wire:click="checkForUpdates" wire:loading.attr="disabled">
    <span wire:loading wire:target="checkForUpdates">Verificando...</span>
    <span wire:loading.remove>Verificar Tabelas</span>
</button>

@if($showUpdateModal)
    <x-modal wire:model="showUpdateModal">
        {{-- resultado --}}
    </x-modal>
@endif
```

---

## Prevenção / Checklist para novos scrapers

- [ ] Sempre criar `OFFICIAL_BRACKETS` como fallback hardcoded
- [ ] Definir epsilon adequado para o domínio (tributário: 0.01)
- [ ] Scraper retorna formato igual ao banco (não converter aqui)
- [ ] Separar Scraper e Comparador em services independentes
- [ ] Logar quando fallback é usado (`Log::warning`)
- [ ] UI deve mostrar fonte usada ("Online" vs "Cache local")

---

## Referências
- Plano: `.aidev/plans/history/2026-02/tax-brackets-verification.md`
- `app/Services/TaxBracketScraperService.php`
- `app/Services/TaxBracketComparatorService.php`
- `app/Livewire/TaxTablesManager.php`
