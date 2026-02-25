# 📸 Snapshot — Calculadora DAS

**Data:** 2026-02-24  
**Estado:** Estável — Sprint 1 completa, container funcionando, banco correto

---

## 🐳 Infraestrutura

| Item | Valor |
|---|---|
| Container | `calculadora-das` (ID: 283aaf0db713) |
| Imagem | `das-das:latest` |
| Porta | `0.0.0.0:8080->80/tcp` |
| Banco | `./storage/app/database.sqlite` (bind mount) |
| `.env` DB | `/home/nandodev/projects/das/storage/app/database.sqlite` |
| URL | http://localhost:8080 |

### Comandos essenciais
```bash
# Subir container
docker compose up --build -d

# Reset completo do banco (preserva usuário via seeder)
docker exec calculadora-das php artisan migrate:fresh --seed

# Limpar caches
docker exec calculadora-das php artisan cache:clear && php artisan view:clear

# Ver logs
docker logs calculadora-das --tail 50
```

---

## 👤 Acesso

| Campo | Valor |
|---|---|
| Email | `angelica.domingos@hotmail.com` |
| Senha | `kinnuty21star` |

---

## 🏗️ Arquitetura — Convenção Tributária

```
TaxBracket (BD)  →  PERCENTUAL  (6 = 6%, 43.4 = 43,4%)
    │
    │ DasCalculatorService.getAliquotaTable() / getTributosTable()
    │ divide por 100 ao ler → trabalha internamente em DECIMAL
    ▼
DasCalculatorService.calcular()
    │ retorna decimais: aliquota_efetiva=0.06, irpj_percent=0.04
    ▼
Calculation (BD)  →  DECIMAL  (0.06 = 6%, 0.04 = 4%)
    │
    ├─ das-calculator.blade.php      → * 100 para exibir
    └─ calculation-history.blade.php → * 100 para exibir

TaxTablesManager blade  →  lê TaxBracket direto  →  SEM * 100
```

**Regra de ouro:** `DasCalculatorService` é o ÚNICO lugar que faz cálculos de DAS.

---

## 📁 Arquivos Principais

| Arquivo | Responsabilidade |
|---|---|
| `app/Services/DasCalculatorService.php` | Cálculo DAS, leitura de tabelas (÷100) |
| `app/Livewire/TaxTablesManager.php` | CRUD faixas tributárias |
| `app/Livewire/DasCalculator.php` | Calculadora principal |
| `app/Livewire/CalculationHistory.php` | Histórico |
| `app/Livewire/Auth/Login.php` | Autenticação com throttle |
| `app/Services/TaxBracketScraperService.php` | Scraper legislação + OFFICIAL_BRACKETS |
| `app/Services/TaxBracketComparatorService.php` | Comparador (epsilon=0.01) |
| `database/seeders/TaxBracketSeeder.php` | Dados em PERCENTUAL (6, 4, 3.5...) |
| `database/seeders/DatabaseSeeder.php` | Cria usuário Angelica + chama TaxBracketSeeder |
| `resources/views/livewire/tax-tables-manager.blade.php` | Tabelas sem `* 100` |
| `resources/views/livewire/das-calculator.blade.php` | Calculadora com `* 100` |
| `resources/views/livewire/calculation-history.blade.php` | Histórico com `* 100` |
| `docker-compose.yml` | Bind mount + env vars |
| `.env` | DB_DATABASE aponta para storage/app/ |

---

## ✅ Sprint 1 — Entregues

- Design System (Air Force Blue, Glassmorphismo, dark mode, components)
- Login seguro (Livewire, throttle, rotas protegidas)
- Calculadora DAS (RBT12, alíquota efetiva, repartição tributos)
- Histórico de cálculos por período
- Gerenciamento de receitas mensais
- Tabelas tributárias editáveis (Anexo III)
- Verificação de atualização das tabelas (scraper + comparador + modal)
- Arquitetura tributária unificada (fonte única de verdade)
- Migração Docker volume → bind mount

---

## 📋 Backlog / Próximas Ideias

*Vazio — aguardando priorização.*

Planos em `.aidev/plans/` | ROADMAP em `.aidev/plans/ROADMAP.md`

---

## 🗃️ Banco de Dados — Estrutura

```sql
tax_brackets:  faixa, min_rbt12, max_rbt12, aliquota_nominal(%), deducao,
               irpj(%), csll(%), cofins(%), pis(%), cpp(%), iss(%)

calculations:  month, year, rpa, rbt12, tax_bracket,
               aliquota_nominal(dec), aliquota_efetiva(dec), valor_total_das,
               irpj_percent(dec), irpj_value, csll_percent(dec), ...

revenues:      month, year, amount, user_id
users:         id, name, email, password
```
