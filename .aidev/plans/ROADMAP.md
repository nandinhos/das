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
- ✅ Design System completo (Air Force Blue, Glassmorphismo, dark mode)
- ✅ Autenticação segura (Login, Livewire, rotas protegidas, throttle)
- ✅ Calculadora DAS funcional (RBT12, alíquota efetiva, repartição de tributos)
- ✅ Histórico de cálculos
- ✅ Gerenciamento de receitas
- ✅ Tabelas tributárias editáveis (Anexo III — Simples Nacional)
- ✅ Verificação de atualização das tabelas (scraper + comparador + UI)
- ✅ Arquitetura tributária — fonte única de verdade (DasCalculatorService)
- ✅ Infraestrutura Docker com bind mount (BD unificado host/container)

---

## 🎯 PRÓXIMAS SPRINTS (A DEFINIR)

*Sem sprints planejadas. Adicione ideias ao backlog para priorização.*

---

## 🔄 FLUXO DE TRABALHO

1. **Nova ideia**: Adicionar em `backlog/` com descrição e prioridade
2. **Priorizar**: Mover para `features/` com plano detalhado
3. **Executar**: Mover para `current/` e registrar no ROADMAP
4. **Concluir**: Mover para `history/YYYY-MM/`

---

## 🏗️ ESTADO ATUAL DA APLICAÇÃO

### Funcionalidades implementadas:
- Login com proteção de rotas (middleware auth)
- Cálculo de DAS (Anexo III — Simples Nacional)
- 6 faixas tributárias editáveis via interface
- Histórico de cálculos por período
- Gerenciamento de receitas mensais
- Verificação automática de atualizações das tabelas tributárias

### Convenções técnicas:
- `TaxBracket` (BD): percentual (6 = 6%)
- `Calculation` (BD): decimal (0.06 = 6%)
- `DasCalculatorService`: única fonte de cálculo; divide por 100 ao ler TaxBracket
- Banco: `storage/app/database.sqlite` (bind mount Docker ↔ host)

---

**Versão:** 2.0
**Última atualização:** 2026-02-24
**Status:** Ativo
