# 🗺️ ROADMAP DE IMPLEMENTAÇÃO - Calculadora DAS

> Documento mestre de planejamento de funcionalidades
> Formato: AI Dev Superpowers Sprint Planning
> Status: Ativo

---

## 📋 VISÃO GERAL

Este documento serve como **fonte única de verdade** para implementação de funcionalidades no projeto.
- ✅ Continuidade entre sessões de desenvolvimento
- ✅ Troca de LLM sem perda de contexto
- ✅ Implementação gradual por sprints
- ✅ Rastreabilidade de decisões

---

## ✅ SPRINTS CONCLUÍDOS

### Sprint 1: Fundação (2026-02)

#### Core da aplicação
- ✅ Design System completo (Air Force Blue, Glassmorphismo, dark mode)
- ✅ Autenticação segura (Login, Livewire, rotas protegidas, throttle)
- ✅ Calculadora DAS funcional (RBT12, alíquota efetiva, repartição de tributos)
- ✅ Histórico de cálculos
- ✅ Gerenciamento de receitas
- ✅ Tabelas tributárias editáveis (Anexo III — Simples Nacional)
- ✅ Infraestrutura Docker com bind mount (BD unificado host/container)

#### Qualidade de dados tributários
- ✅ Scraper do Planalto (`TaxBracketScraperService`) — extração via Regex (11 campos)
- ✅ Comparador de tabelas (`TaxBracketComparatorService`) — checksum SHA-256
- ✅ View de Diagnóstico (`/diagnostico`) — Pipeline, JSON colorido, badge SCRAPING/FALLBACK
- ✅ Botão "Corrigir Tabelas" — atualiza banco e registra versão com modal Alpine.js
- ✅ Versionamento de tabelas tributárias (`tax_bracket_versions`) com snapshots JSON
- ✅ Remoção da constante `OFFICIAL_BRACKETS` hardcoded

#### Refatorações e arquitetura
- ✅ `DasCalculatorService` como única fonte de verdade do cálculo
- ✅ Fix porcentagens x100 no frontend (convenção BD vs UI)
- ✅ Timezone e locale: `America/Sao_Paulo` + `pt_BR`

### Sprint 2: Mobile-First UI Review (2026-02)
- ✅ Header sempre `flex-row` — elimina empilhamento vertical até 768px
- ✅ Logo compacto em mobile, CNPJ oculto em mobile
- ✅ Tabs com shortLabels: "Receitas/Calcular/Histórico/Tabelas" em mobile
- ✅ Tabela Alíquotas (4 cols): card layout em mobile com campos editáveis
- ✅ Tabela Repartição (7 cols): card layout mobile com grid 2×3 por faixa
- ✅ Inputs mobile sem `absolute` positioning (`inline`, `w-full`, `inputmode=decimal`)
- ✅ Modal de verificação responsivo (`flex-col-reverse`, `overflow-x-auto`, border-radius toque)

---

## 📋 BACKLOG (Ideias Priorizáveis)

*Sem itens no momento. Adicione ideias ao backlog para priorização.*

---

## 🎯 PRÓXIMAS SPRINTS (A DEFINIR)

Para iniciar nova sprint:
1. Escolher item(ns) do backlog
2. Mover para `features/` com plano detalhado
3. Registrar aqui como "Sprint N: Nome (YYYY-MM)"
4. Executar e mover para `history/`

---

## 🔄 FLUXO DE TRABALHO

```
Nova ideia → backlog/
Priorizada → features/  (com plano detalhado)
Em execução → current/
Concluída → history/YYYY-MM/
```

---

## 🏗️ ESTADO ATUAL DA APLICAÇÃO

### Funcionalidades implementadas

| Área | Funcionalidade | Status |
|---|---|---|
| Auth | Login com proteção de rotas | ✅ |
| Cálculo | DAS Anexo III — Simples Nacional | ✅ |
| Tributário | 6 faixas editáveis via interface | ✅ |
| Tributário | Versionamento de tabelas (snapshots JSON) | ✅ |
| Tributário | Scraper do Planalto (Regex, 11 campos) | ✅ |
| Tributário | Comparador com checksum SHA-256 | ✅ |
| Dev Tool | `/diagnostico` — diagnóstico do scraper | ✅ |
| Dev Tool | Botão "Corrigir Tabelas" com modal | ✅ |
| Histórico | Cálculos por período | ✅ |
| Receitas | Gerenciamento mensal | ✅ |
| Infra | Docker + bind mount SQLite | ✅ |

### Convenções técnicas

| Convenção | Detalhe |
|---|---|
| `TaxBracket` (BD) | Percentual inteiro (6 = 6%) |
| `Calculation` (BD) | Decimal (0.06 = 6%) |
| `DasCalculatorService` | Única fonte de cálculo; divide por 100 ao ler TaxBracket |
| Banco | `storage/app/database.sqlite` (bind mount Docker ↔ host) |
| Timezone | `America/Sao_Paulo` |
| Locale | `pt_BR` |

---

**Versão:** 3.0
**Última atualização:** 2026-02-25
**Status:** Ativo
