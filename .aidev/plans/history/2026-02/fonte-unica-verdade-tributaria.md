# Feature: Arquitetura Tributária — Fonte Única de Verdade

**Status:** Aguardando aprovação
**Data:** 2026-02-24
**Tipo:** Bug Fix + Definição Arquitetural
**Unifica:** `fix-tax-percentages-display.md` + `unify-calculation-logic.md`
**Estimativa:** 30 min (código já feito; falta reset do banco + validação)

---

## Por que ainda mostra 600%

O banco atualmente tem `aliquota_nominal = 600` porque:

1. O volume Docker antigo tinha `6.0` (formato percentual, correto)
2. Executamos `UPDATE SET aliquota_nominal = aliquota_nominal * 100` erroneamente → `6.0 * 100 = 600`
3. O blade foi corrigido (sem `* 100`), então `600` é exibido diretamente como `600%`

**Solução:** `migrate:fresh --seed` dentro do container para resetar o banco com o seeder já corrigido.

---

## Arquitetura Definida (Fonte Única de Verdade)

```
TaxBracket (BD)
  └─ formato PERCENTUAL: aliquota_nominal=6, irpj=4, csll=3.5 ...
       │
       │ DasCalculatorService lê e ÷ 100 internamente
       ▼
DasCalculatorService (única fonte de cálculo)
  ├─ getAliquotaTable() → nominal = 6/100 = 0.06 (decimal para fórmulas)
  ├─ getTributosTable() → irpj = 4/100 = 0.04 (decimal para fórmulas)
  └─ calcular() retorna decimais: aliquota_nominal=0.06, irpj_percent=0.04 ...
       │
       │ salva resultado
       ▼
Calculation (BD)
  └─ formato DECIMAL: aliquota_nominal=0.06, irpj_percent=0.04 ...
       │
       ├─ das-calculator.blade.php → * 100 para exibir → 6%, 4% ✓
       └─ calculation-history.blade.php → * 100 para exibir → 6%, 4% ✓

TaxTablesManager (Livewire)
  └─ lê TaxBracket direto (BD percentual) → sem * 100 → exibe 6%, 4% ✓
```

### Tabela de Responsabilidades

| Componente | Formato | Responsabilidade | Status |
|---|---|---|---|
| `TaxBracket` (BD) | Percentual `6` | Armazenamento das tabelas da lei | ✓ (após reset) |
| `TaxBracketSeeder` | Percentual `6, 4, 3.5...` | Seed correto | ✓ já corrigido |
| `DasCalculatorService.getAliquotaTable()` | Decimal `0.06` | Divide por 100 ao ler BD | ✓ já corrigido |
| `DasCalculatorService.getTributosTable()` | Decimal `0.04` | Divide por 100 ao ler BD | ✓ já corrigido |
| `DasCalculatorService.calcular()` | Decimal | Única fonte de cálculo | ✓ sem alteração |
| `TaxTablesManager.updateBracket()` | Percentual `6` | Salva sem ÷ 100 | ✓ já corrigido |
| `TaxTablesManager` blade | Percentual (BD direto) | Sem `* 100` | ✓ já corrigido |
| `das-calculator.blade.php` | `decimal * 100` | Exibe resultado do DasCalculatorService | ✓ sem alteração |
| `calculation-history.blade.php` | `decimal * 100` | Exibe do model Calculation (decimal) | ✓ sem alteração |
| `TaxBracketScraperService` `OFFICIAL_BRACKETS` | Percentual `6` | Fallback de referência | ✓ já corrigido |
| `TaxBracketScraperService.extractPercentage()` | Percentual | Remove `/ 100` ao parsear HTML | ✓ já corrigido |
| `TaxBracketComparatorService` epsilon | `0.01` | Adequado para comparar 6 vs 6 | ✓ já corrigido |
| `Calculation` (BD) | Decimal `0.06` | Histórico de cálculos | ✓ sem alteração |

---

## Tasks Pendentes

### Task 1 — Reset do Banco [CRÍTICO, 5 min]

Dentro do container, rodar:

```bash
php artisan migrate:fresh --seed
```

Resultado esperado:
- Usuário `angelica.domingos@hotmail.com` / `kinnuty21star` recriado pelo `DatabaseSeeder`
- `tax_brackets`: `aliquota_nominal=6`, `irpj=4`, `csll=3.5`, etc.
- Histórico de cálculos e receitas zerado (reset mesmo)

### Task 2 — Rebuild do Container [5 min]

```bash
docker compose build --no-cache && docker compose up -d
```

### Task 3 — Verificação [10 min]

Acessar `http://localhost:8080` com aba anônima:

- [ ] Tabela de alíquotas: Faixa 1 = `6,00%`, Faixa 6 = `33,00%`
- [ ] Tabela de tributos: IRPJ = `4,00%`, CPP = `43,40%`
- [ ] Fazer um cálculo de DAS e conferir os valores no histórico
- [ ] Editar uma célula na tabela e confirmar que salva corretamente

### Task 4 — Atualizar Planos [5 min]

- Marcar `fix-tax-percentages-display.md` como RESOLVIDO (convenção: percentual no BD)
- Marcar `unify-calculation-logic.md` como CONCLUÍDO
- Atualizar README do backlog

---

## O Que JÁ Foi Implementado (esta sessão)

| Arquivo | Mudança |
|---|---|
| `tax-tables-manager.blade.php:50` | Removido `* 100` (aliquota_nominal) |
| `tax-tables-manager.blade.php:110` | Removido `* 100` (tributos) |
| `TaxTablesManager.php:updateBracket()` | Removido `/ 100` |
| `DasCalculatorService.getAliquotaTable()` | Adicionado `/ 100` ao ler `aliquota_nominal` |
| `DasCalculatorService.getTributosTable()` | Adicionado `/ 100` em irpj, csll, cofins, pis, cpp, iss |
| `TaxBracketSeeder.php` | Valores em percentual (6, 4, 3.5...) |
| `TaxBracketScraperService.php` OFFICIAL_BRACKETS | Valores em percentual |
| `TaxBracketScraperService.extractPercentage()` | Removido `/ 100` |
| `TaxBracketComparatorService.php` | Epsilon `0.0001` → `0.01` |
| `docker-compose.yml` | Bind mount `./storage` (unifica BD host+container) |

---

## Convenção Definitiva (para CLAUDE.md / onboarding)

```
BANCO DE DADOS:
  TaxBracket   → percentual (6 = 6%, 43.4 = 43,4%)
  Calculation  → decimal   (0.06 = 6%, 0.04 = 4%)

SERVIÇO:
  DasCalculatorService é o ÚNICO responsável por cálculos de DAS.
  Ao ler TaxBracket, sempre divide por 100 antes de calcular.

VIEWS:
  tax-tables-manager → lê TaxBracket direto → sem * 100
  das-calculator, calculation-history → leem DasCalculatorService/Calculation → com * 100
```
