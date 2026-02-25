# Backlog — View de Diagnóstico do Scraper Tributário

**Data de criação:** 2026-02-24
**Prioridade:** Média
**Tipo:** Dev Tool / Debugging
**Estimativa:** ~2h

---

## Objetivo

Criar uma página separada `/diagnostico` para inspecionar visualmente a saída do scraper tributário — estrutura, formato, qualidade dos dados extraídos — e verificar na prática se o scraping online está funcional ou se o sistema está sempre caindo no fallback.

---

## Contexto / Motivação

O `TaxBracketScraperService` tenta extrair dados do Planalto e usa `OFFICIAL_BRACKETS` como fallback silencioso. Sem esta view, não há forma visual de saber se o scraping web está funcionando ou se o sistema sempre usa os dados hardcoded.

### Bug identificado (a ser evidenciado pela view)

`parseRow()` extrai **apenas 5 campos** do HTML do Planalto:
`faixa`, `min_rbt12`, `max_rbt12`, `aliquota_nominal`, `deducao`

O fallback `OFFICIAL_BRACKETS` tem **11 campos** (inclui `irpj`, `csll`, `cofins`, `pis`, `cpp`, `iss`).

Ou seja, mesmo que o scraping web funcione, os tributos por repartição **nunca são extraídos da fonte online**. A view de diagnóstico tornará isso imediatamente visível, justificando uma correção futura do `parseRow()`.

Adicionalmente, `checkForUpdates()` retorna `'source' => 'site_planalto'` mesmo quando o fallback é aplicado silenciosamente (bug de observabilidade).

---

## Arquivos a Criar

| Arquivo | Descrição |
|---|---|
| `app/Livewire/ScraperDiagnostic.php` | Livewire full-page component |
| `resources/views/livewire/scraper-diagnostic.blade.php` | View do diagnóstico |
| `resources/views/layouts/diagnostic.blade.php` | Layout minimalista (header + body, sem tabs) |

## Arquivos a Modificar

| Arquivo | Mudança |
|---|---|
| `routes/web.php` | `GET /diagnostico` → `ScraperDiagnostic::class` (middleware auth) |

---

## Seções da View

### Header
- Título "Diagnóstico do Scraper Tributário"
- Link "← Voltar ao App"
- Botão "Executar Diagnóstico" (lazy — só roda ao clicar, não no mount)
- Loading spinner durante execução

### Seção 1 — Teste de Conexão HTTP
Teste direto ao Planalto, independente do scraper:
- Badge: 🟢 ONLINE / 🔴 OFFLINE / ⚠️ TIMEOUT
- HTTP status code + tempo de resposta (ms)
- URL testada

### Seção 2 — Dados Extraídos (Web)
Tabela com o retorno de `fetchOfficialBrackets()`:
- Badge "🟢 SCRAPING WEB" ou "🔴 FALLBACK APLICADO"
- Campos ausentes (`irpj`, `csll`, etc.) destacados em vermelho
- Alerta explícito sobre o bug do `parseRow()` se confirmado

### Seção 3 — OFFICIAL_BRACKETS (Referência Hardcoded)
Tabela com todos os 11 campos como referência visual

### Seção 4 — Resultado do Comparador
Resultado de `checkForUpdates()`:
- Status badge (uptodate / outdated / error)
- Alerta se `source=site_planalto` mas fallback foi aplicado na prática
- Tabela de diferenças (se houver)
- Bloco JSON expansível com payload completo

---

## Lógica do Componente Livewire

```php
public function run(): void
{
    // 1. Teste HTTP direto (timing + status)
    $start = microtime(true);
    $response = Http::timeout(15)->get(PLANALTO_URL);
    $this->connectionTest = [status, status_code, duration_ms, error];

    // 2. Dados do scraper (pode ser web ou fallback)
    $scraper = app(TaxBracketScraperService::class);
    $this->scraped = $scraper->fetchOfficialBrackets();
    $this->fallback = $scraper->getOfficialBracketsFallback();

    // 3. Inferência: usou fallback?
    // Se conexão falhou OU campos de tributos ausentes nos scraped → usedFallback = true
    $this->usedFallback = !$this->connectionTest['success'] ||
        empty(array_filter($this->scraped, fn($b) => isset($b['irpj'])));

    // 4. Resultado do comparador
    $this->comparisonResult = app(TaxBracketComparatorService::class)->checkForUpdates();
}
```

---

## Critérios de Aceite

- [ ] Página acessível em `/diagnostico` (protegida por auth)
- [ ] Botão "Executar" roda o diagnóstico e exibe resultados
- [ ] Seção 1 mostra se o site do Planalto está acessível e o tempo de resposta
- [ ] Seção 2 revela claramente que o scraping web extrai apenas 5 campos (sem tributos)
- [ ] Seção 3 exibe os 11 campos do OFFICIAL_BRACKETS como referência
- [ ] Seção 4 exibe o resultado completo do comparador
- [ ] Badge "FALLBACK APLICADO" aparece quando conexão falha ou campos ausentes
- [ ] Design consistente com o sistema existente (glassmorphism, dark mode, x-das.* components)

---

## Dependências

- `TaxBracketScraperService` (já existe)
- `TaxBracketComparatorService` (já existe)
- Design system: `x-das.section`, `x-das.button`, Tailwind, Alpine.js (já existem)
- Laravel Http Client (built-in)

---

## Notas para Implementação

- **Não modificar** `TaxBracketScraperService` ou `TaxBracketComparatorService` — apenas visualizar
- Após criar a view diagnóstica, o bug do `parseRow()` estará evidenciado → criar novo item de backlog para corrigir o scraping e extrair os campos de tributos
- O layout `diagnostic.blade.php` pode ser base para futuras páginas de admin/dev tools
