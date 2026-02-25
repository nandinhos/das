# Lição: Refatoração do Scraper Tributário — Regex, Diagnóstico e Alpine.js

**Data**: 2026-02-25
**Stack**: Laravel 11 + PHP 8.3 + Livewire 4 + Alpine.js 3
**Tags**: scraping, regex, laravel, encoding, livewire, diagnóstico, planalto, performance
**Tipo**: Refatoração + Padrões Técnicos

---

## Contexto

`TaxBracketScraperService` fazia scraping do site do Planalto para extrair as tabelas do Anexo III
do Simples Nacional. A implementação original usava DomCrawler e extraía apenas 5 dos 11 campos necessários.

Paralelamente, foi criada a página `/diagnostico` para inspecionar visualmente o scraper.

---

## Lição 1: DomCrawler vs Regex para HTML Governamental

### Problema
- Site do Planalto: ~2MB de HTML em **ISO-8859-1**
- DomCrawler falha ou dá timeout em tabelas HTML governamentais mal-formatadas
- `parseRow()` com DomCrawler extraía apenas 5 campos (faixa, receita, alíquota, dedução + 1 extra)
- Faltavam os 6 campos de repartição: IRPJ, CSLL, COFINS, PIS, CPP, ISS

### Solução: Regex com pré-filtragem

```php
// Estratégia: localizar tabela por âncora de texto, depois capturar rows
$pos = strrpos($html, 'RECEITA BRUTA');
$tableHtml = substr($html, $pos, 5000);

// Regex para capturar células <td> por linha
preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tableHtml, $rows);
foreach ($rows[1] as $row) {
    preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $row, $cells);
    $values = array_map('strip_tags', $cells[1]);
}
```

**Resultado**: 30x mais rápido que DomCrawler; extrai todos os 11 campos corretamente.

### Regra
Para HTML governamental (mal-formatado, pesado, ISO-8859-1): prefira Regex com pré-filtragem a parsers DOM.

---

## Lição 2: Merge de Duas Tabelas do Anexo III

### Estrutura do Planalto
O Anexo III tem **duas tabelas separadas**:
1. **Tabela de Alíquotas**: faixa, receita bruta, alíquota nominal, parcela a deduzir
2. **Tabela de Repartição**: faixa, IRPJ%, CSLL%, COFINS%, PIS/PASEP%, CPP%, ISS%

### Solução: Merge por Índice de Faixa
```php
$aliquotas = $this->parseAliquotasTable($html);  // 6 rows
$reparticao = $this->parseReparticaoTable($html); // 6 rows

$merged = [];
foreach ($aliquotas as $index => $row) {
    $merged[] = array_merge($row, $reparticao[$index] ?? []);
}
```

**Invariante**: ambas as tabelas sempre têm exatamente 6 faixas — o merge por índice é seguro.

---

## Lição 3: Encoding ISO-8859-1 do Planalto

### Problema
Acentos e caracteres especiais aparecem como `?` ou `â€` se não convertidos.

### Solução Obrigatória
```php
$response = Http::get($url);
$body = mb_convert_encoding($response->body(), 'UTF-8', 'ISO-8859-1');
```

Esta linha é **obrigatória** antes de qualquer processamento de texto do Planalto.

---

## Lição 4: Novas Propriedades Livewire Exigem Rebuild do Container

### Problema
Adicionar propriedades públicas ao componente Livewire (`public array $scraperMeta = []`)
não é refletido na interface se existe cache Docker antigo.

### Solução
```bash
docker compose down && docker compose up -d --build
```

**Não confundir** com `php artisan cache:clear` — o problema é o container em si, não o cache Laravel.

---

## Lição 5: JSON Syntax Highlighting com Alpine.js + Blade

Documentado em detalhes em `2026-02-25-livewire-alpine-modal-confirm-pattern.md` (seção Bônus).

Resumo: usar `window.highlightJson(@js($data))` em vez de expressões Alpine inline nos atributos HTML.

---

## Arquitetura Final do Scraper

```
TaxBracketScraperService::fetchOfficialBrackets()
    │
    ├─ Tenta: GET planalto.gov.br (timeout 10s)
    │    ├─ mb_convert_encoding (ISO-8859-1 → UTF-8)
    │    ├─ parseAliquotasTable() → Regex
    │    ├─ parseReparticaoTable() → Regex
    │    └─ merge() → 6 faixas × 11 campos
    │
    └─ Fallback (se timeout/erro): OFFICIAL_BRACKETS (constante)
         └─ Nota: constante será substituída por JSON versionado
            (backlog: sync-official-brackets-fallback.md)
```

---

## Referências

| Arquivo | Conteúdo |
|---------|----------|
| `app/Services/TaxBracketScraperService.php` | Implementação completa Regex |
| `app/Livewire/ScraperDiagnostic.php` | Componente diagnóstico |
| `resources/views/livewire/scraper-diagnostic.blade.php` | View completa |
| `.aidev/plans/backlog/scraper-diagnostic-view.md` | Backlog: feature concluída |
| `lessons-learned/Refatoração do Scraper...` | Nota basic-memory (cross-project) |

## Commits de Referência

- `5a96095` — Refactor: Scraper Regex + diagnóstico com Pipeline e JSON colorido
- `4dfa7f1` — Feat: Botão Corrigir no diagnóstico
