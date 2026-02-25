# 🏃 Current - Em Execução

> Sprint ativa sendo executada no momento

---

## 🎯 Sprint Ativa

**Sprint 2: Mobile-First UI Review (2026-02)**

Plano completo: [features/mobile-first-ui-review.md](../features/mobile-first-ui-review.md)

### Arquivos que serão modificados

| Arquivo | O que muda |
|---|---|
| `resources/views/layouts/app.blade.php` | Header sempre `flex-row`, shortLabels nas tabs móbile |
| `resources/views/livewire/tax-tables-manager.blade.php` | Cards mobile para 2 tabelas + modal responsivo |

### Arquivos verificados e OK (sem alteração necessária)

| Arquivo | Motivo |
|---|---|
| `resources/views/livewire/revenue-manager.blade.php` | Já tem padrão card/tabela implementado ✅ |
| `resources/views/livewire/scraper-diagnostic.blade.php` | Já usa `overflow-x-auto`, grids responsivos ✅ |
| `resources/views/livewire/das-calculator.blade.php` | Tabela 3 colunas OK com `overflow-x-auto` do `das-table-wrapper` ✅ |
| `resources/views/livewire/calculation-history.blade.php` | Padrão accordion funcional em mobile ✅ |
| `resources/views/components/das/table-wrapper.blade.php` | Já tem `overflow-x: auto` via CSS ✅ |

### Análise de problemas identificados

**`app.blade.php`:**
- Header usa `flex-col md:flex-row` → empilha verticalmente até 768px, desperdiçando espaço
- Tabs labels: "Dashboard Receitas" e "Tabelas Tributárias" são longos para grid 2x2 em 375px

**`tax-tables-manager.blade.php`:**
- Tabela Alíquotas (4 cols): "Receita Bruta" tem texto como "De R$ 180.000,01 a R$ 360.000,00" — muito largo
- Tabela Repartição (7 cols): IRPJ + CSLL + Cofins + PIS + CPP + ISS — impossível sem scroll
- Inputs com `absolute right-4 top-1/2 -translate-y-1/2` quebraria em cards sem ajuste
- Modal de verificação com tabelas internas sem `overflow-x-auto` no wrapper

---

## 📊 Status

- **Sprint atual:** Sprint 2 — Mobile-First UI Review
- **Iniciada em:** 2026-02-25
- **Estimativa:** 1 sessão

---

*Última atualização: 2026-02-25*
