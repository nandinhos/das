# Lição: Arquitetura Tributária — Convenção Percentual vs Decimal

**Data**: 2026-02-24
**Stack**: Laravel 11 + Livewire 4 + SQLite
**Tags**: arquitetura, tributário, simples-nacional, convenção, bug, data-model
**Severidade**: Crítico — causou exibição de 600% em vez de 6%

---

## Contexto

Sistema de cálculo de DAS (Simples Nacional) com tabelas tributárias do Anexo III.
Duas tabelas no banco: `tax_brackets` (fonte das alíquotas) e `calculations` (histórico).
Múltiplos serviços e views precisam ler e exibir os mesmos percentuais.

---

## O Bug

### Sintoma
Tabela de alíquotas exibia `600%` em vez de `6%`.

### Causa Raiz (Cadeia de Erros)
```
1. Volume Docker antigo: aliquota_nominal = 6.0  (percentual, correto)
2. Executamos UPDATE SET aliquota_nominal = aliquota_nominal * 100  (errado!)
   → 6.0 * 100 = 600  (banco corrompido)
3. A view foi corrigida para remover * 100
   → 600 exibido diretamente como 600%
```

O problema real era a **ausência de uma convenção explícita e documentada** sobre qual formato cada camada deveria usar.

---

## Solução: Arquitetura de Fonte Única de Verdade

### Convenção Definitiva

```
TaxBracket (BD)
  └─ formato PERCENTUAL: aliquota_nominal=6, irpj=4, csll=3.5 ...
       │
       │ DasCalculatorService lê e ÷ 100 internamente
       ▼
DasCalculatorService  ← ÚNICA FONTE DE CÁLCULO
  ├─ getAliquotaTable() → divide por 100 → 0.06 (decimal para fórmulas)
  ├─ getTributosTable() → divide por 100 → 0.04 (decimal para fórmulas)
  └─ calcular() retorna tudo em decimal
       │
       ▼
Calculation (BD)
  └─ formato DECIMAL: aliquota_nominal=0.06, irpj_percent=0.04 ...
       │
       ├─ das-calculator.blade.php → * 100 para exibir → 6% ✓
       └─ calculation-history.blade.php → * 100 para exibir → 6% ✓

TaxTablesManager (Livewire)
  └─ lê TaxBracket direto (BD percentual) → SEM * 100 → 6% ✓
```

### Regra de Ouro
**`DasCalculatorService` é o ÚNICO responsável por cálculos de DAS.**
Qualquer outro componente que precise calcular deve usar este service.

---

## Mudanças Implementadas

| Arquivo | Mudança |
|---|---|
| `tax-tables-manager.blade.php` | Removido `* 100` (aliquota_nominal e tributos) |
| `TaxTablesManager.php::updateBracket()` | Removido `/ 100` (salvava percentual diretamente) |
| `DasCalculatorService::getAliquotaTable()` | Adicionado `/ 100` ao ler `aliquota_nominal` |
| `DasCalculatorService::getTributosTable()` | Adicionado `/ 100` em irpj, csll, cofins, pis, cpp, iss |
| `TaxBracketSeeder.php` | Valores em percentual (6, 4, 3.5...) |
| `TaxBracketScraperService.php` OFFICIAL_BRACKETS | Valores em percentual |
| `TaxBracketScraperService::extractPercentage()` | Removido `/ 100` (retorna percentual bruto) |
| `TaxBracketComparatorService.php` | Epsilon `0.0001` → `0.01` (adequado para percentuais) |

---

## Lições Aprendidas

### 1. Documente a convenção ANTES de implementar
Defina explicitamente em `CLAUDE.md` ou `SNAPSHOT.md` qual formato cada tabela usa.
Sem isso, qualquer desenvolvedor (ou LLM) pode aplicar `* 100` ou `/ 100` errado.

### 2. Nunca execute UPDATE direto no banco sem rollback planejado
O comando `UPDATE SET aliquota_nominal = aliquota_nominal * 100` foi irreversível sem backup.
Sempre faça backup antes de transformações em produção.

### 3. A camada de serviço é o lugar certo para conversões de unidade
Colocar `/ 100` no `DasCalculatorService` centraliza toda a conversão.
Views e controllers não devem fazer conversões de unidade — apenas exibir o que recebem.

### 4. Quando banco corrompe, `migrate:fresh --seed` é mais seguro que UPDATE
Para dados de referência (tabelas tributárias), o seeder é a fonte de verdade.
Um reset limpo é mais confiável que tentar corrigir dados errados manualmente.

### 5. Epsilon para comparação de percentuais: use 0.01
Comparar `6.0 == 6.0` com epsilon `0.0001` é muito rígido para dados de scraping.
Use `epsilon = 0.01` para tabelas tributárias (diferenças de 0,01 são irrelevantes).

---

## Prevenção

- [ ] Manter `SNAPSHOT.md` atualizado com a convenção (TaxBracket=percentual, Calculation=decimal)
- [ ] Nunca adicionar `* 100` ou `/ 100` em views de tax-tables-manager (lê BD direto)
- [ ] Sempre adicionar `* 100` em views de das-calculator e calculation-history (recebem decimal)
- [ ] Qualquer novo campo percentual em `TaxBracket` deve ser dividido por 100 em `DasCalculatorService`
- [ ] Antes de qualquer UPDATE em massa no banco, criar backup: `cp database.sqlite database.sqlite.bak`

---

## Referências
- Plano: `.aidev/plans/history/2026-02/fonte-unica-verdade-tributaria.md`
- Plano: `.aidev/plans/history/2026-02/fix-tax-percentages-display.md`
- Snapshot: `.aidev/SNAPSHOT.md` (seção Arquitetura Tributária)
