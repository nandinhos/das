# Backlog: Upgrade do Agente frontend-responsivo — Padrões de Tabelas em Modais

**Status**: backlog
**Prioridade**: média
**Criado**: 2026-02-25
**Complexidade**: Alta — requer validação externa antes de aplicar
**Bloqueante**: Context7 + Laravel Boost (validação obrigatória)

---

## Objetivo

Incorporar os padrões mobile-first validados na Sprint 2 ao agente `frontend-responsivo.md` no projeto base do orquestrador, para que futuras implementações já utilizem as práticas corretas sem descoberta empírica.

---

## Contexto

Durante a Sprint 2 do projeto `das`, identificamos dois padrões críticos que o agente atual NÃO instrui:

1. **`min-w-full` invalida `overflow-x-auto` em modais** — o agente corrige o container mas não a classe da tabela; o problema persiste silenciosamente
2. **Card Layout para tabelas com 4+ colunas** — UX superior ao scroll horizontal em containers estreitos

O draft técnico completo está em:
`.aidev/memory/kb/2026-02-25-draft-agent-responsivo-tables-upgrade.md`

---

## Fases de Execução

### Fase 1: Validação (BLOQUEANTE)
Antes de qualquer modificação no agente:

**Via Context7:**
- [ ] Confirmar que `min-w-max` é suportado no Tailwind CSS 4
- [ ] Confirmar comportamento de `min-w-max w-full` combinados
- [ ] Verificar documentação oficial de padrões para tabelas em modais

**Via Laravel Boost:**
- [ ] Confirmar que `das-card` e `das-text-muted` são classes globais reutilizáveis ou se precisam de alternativa genérica
- [ ] Confirmar que `sm:hidden` + `hidden sm:block` é consistente com guidelines do projeto
- [ ] Verificar conflito com Livewire morphing ao alternar card/table no mesmo componente

### Fase 2: Consolidação do Draft
- [ ] Revisar `2026-02-25-draft-agent-responsivo-tables-upgrade.md` com base nos resultados da validação
- [ ] Ajustar os snippets de código se Context7/Laravel Boost indicarem variações
- [ ] Decidir se as classes `das-card` viram genéricas no documento do agente

### Fase 3: Aplicação ao Agente (no projeto base do orquestrador)
- [ ] Abrir o projeto base do orquestrador
- [ ] Editar `frontend-responsivo.md`:
  - Atualizar seção "Tabela com scroll horizontal" com Padrão 1 (`min-w-max`)
  - Adicionar Padrão 2 (Card Layout)
  - Adicionar seção "Diagnóstico de Tabelas em Containers Estreitos"
  - Atualizar Checklist de Auditoria
  - Atualizar Anti-Patterns
- [ ] Sincronizar com o `frontend-responsivo.md` local do projeto `das`

### Fase 4: Validação Final
- [ ] Abrir nova sessão de teste com o agente atualizado
- [ ] Simular cenário de modal com tabela 4 colunas
- [ ] Verificar que o agente propõe o Card Layout (não apenas scroll)

---

## Critérios de Aceite

- [ ] Agente instrui corretamente `min-w-max` vs `min-w-full` com decisão contextual
- [ ] Agente instrui o Card Layout como padrão preferido para 4+ colunas em modais
- [ ] Agente inclui árvore de decisão para diagnóstico de tabelas
- [ ] Checklist de auditoria inclui verificação de `max-w-*` nos containers
- [ ] Padrões validados pelo Context7 e Laravel Boost

---

## Artefatos

| Artefato | Caminho | Status |
|----------|---------|--------|
| KB local de lições | `.aidev/memory/kb/2026-02-25-mobile-first-tables-modals.md` | ✅ Criado |
| Memória global | `global-standards/lessons/mobile-first/mobile-first-tables-in-modals-tailwind-pattern` | ✅ Criado |
| Draft técnico do agente | `.aidev/memory/kb/2026-02-25-draft-agent-responsivo-tables-upgrade.md` | ✅ Draft pronto |
| Agente atualizado | `frontend-responsivo.md` no projeto base | ⏳ Aguarda validação |

---

## Por que está no backlog e não em sprint imediata

- **Dependência externa**: Context7 e Laravel Boost precisam ser consultados — sessão separada
- **Escopo de projeto diferente**: modificação vai para o projeto base do orquestrador, não o `das`
- **Risco de introdução de padrões incorretos**: sem validação, o agente poderia propagar anti-patterns
- **Não bloqueia o desenvolvimento atual**: o `das` já tem os padrões corretos implementados
