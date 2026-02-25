# Backlog — View de Diagnóstico do Scraper Tributário

**Data de criação:** 2026-02-24
**Última atualização:** 2026-02-25
**Prioridade:** Média → ✅ Concluída (total)
**Tipo:** Dev Tool / Debugging
**Estimativa:** ~2h (original) | +1h (botão Corrigir)

---

## Objetivo

Criar uma página separada `/diagnostico` para inspecionar visualmente a saída do scraper tributário — estrutura, formato, qualidade dos dados extraídos — e verificar na prática se o scraping online está funcional ou se o sistema está sempre caindo no fallback.

---

## Contexto / Motivação

O `TaxBracketScraperService` tenta extrair dados do Planalto e usa `OFFICIAL_BRACKETS` como fallback silencioso. Sem esta view, não há forma visual de saber se o scraping web está funcionando ou se o sistema sempre usa os dados hardcoded.

### Bug identificado e CORRIGIDO ✅

O `parseRow()` original (DomCrawler) extraía **apenas 5 campos**. Foi refatorado para **Regex** que agora extrai **todos os 11 campos** corretamente, fazendo merge de duas tabelas (alíquotas + repartição).

---

## Critérios de Aceite

- [x] Página acessível em `/diagnostico` (protegida por auth)
- [x] Botão "Executar" roda o diagnóstico e exibe resultados
- [x] Seção 1 mostra se o site do Planalto está acessível e o tempo de resposta
- [x] Seção 2 revela dados extraídos com badge SCRAPING WEB ATIVO / FALLBACK
- [x] Seção 3 exibe os 11 campos do OFFICIAL_BRACKETS como referência
- [x] Seção 4 exibe o resultado completo do comparador com JSON colorido
- [x] Pipeline de Extração com metadados (fonte, parser, encoding, duração)
- [x] JSON com syntax highlighting (Alpine.js + VS Code colors)
- [x] **Botão "Corrigir Tabelas"** no Comparador (ver plano abaixo)

---

## Plano — Botão "Corrigir Tabelas"

### Objetivo
Quando o comparador detecta diferenças, permitir ao usuário atualizar as tabelas locais com um clique.

### Arquivos a Modificar

| Arquivo | Mudança |
|---|---|
| `app/Livewire/ScraperDiagnostic.php` | Novo método `applyCorrections()` + propriedades de estado |
| `resources/views/livewire/scraper-diagnostic.blade.php` | Botão + feedback visual na seção Comparador |

### Lógica do `applyCorrections()`
```php
public function applyCorrections(): void
{
    // 1. Bloqueia se fonte é fallback
    if ($this->comparisonResult['source'] === 'fallback') {
        $this->correctionMessage = 'Correção indisponível com dados de fallback.';
        return;
    }

    // 2. Atualiza cada TaxBracket com dados oficiais ($this->scraped)
    foreach ($this->scraped as $official) {
        TaxBracket::updateOrCreate(
            ['faixa' => $official['faixa']],
            collect($official)->except('faixa')->toArray()
        );
    }

    // 3. Reexecuta comparador para confirmar sincronização
    $this->comparisonResult = app(TaxBracketComparatorService::class)->checkForUpdates();
    $this->corrected = true;
}
```

### UX do Botão
- Aparece **somente** quando `status === 'outdated'`
- Desabilitado quando fonte é `fallback` (tooltip: "Dados de fallback não são confiáveis")
- Usa `wire:confirm` para confirmação antes de executar
- Após sucesso: badge "SINCRONIZADO ✅" substitui "DESATUALIZADO"
- Spinner durante execução

---

## Notas

- ~~Não modificar TaxBracketScraperService~~ → já foi refatorado (Regex)
- O layout `diagnostic.blade.php` pode ser base para futuras páginas de admin
- Timezone e locale já corrigidos: `America/Sao_Paulo` + `pt_BR`
