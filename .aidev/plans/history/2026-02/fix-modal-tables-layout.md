# Backlog: Ajuste de Tabelas nos Modais de Correção de Tarifas

**Status:** backlog
**Prioridade:** média
**Criado:** 2026-02-25

---

## Problema

As tabelas nos modais de correção tributária apresentam problemas de layout — colunas cortadas e valores truncados — especialmente em telas menores.

### Modais afetados

1. **"Verificação de Tabelas Tributárias"** (modal de diferenças)
   - Coluna "Oficial" aparece truncada como "O"
   - Valores como `9.360,0000` são cortados

2. **"Confirmar Correção"** (modal de confirmação antes de aplicar)
   - Colunas "De" e "Para" ficam apertadas
   - Valores longos como `6.485.000,0000` e `648.000,0000` não cabem

### Comportamento esperado

- Cabeçalhos completos visíveis: Faixa, Campo, Atual, Oficial / De, Para
- Valores numéricos sem truncamento
- Layout responsivo: scroll horizontal no mobile OU card layout por linha
- Consistência com o card layout já adotado na Sprint 2 nas demais tabelas

---

## Contexto técnico

- Os modais já usam `overflow-x-auto` na Sprint 2 (item: "Modal de verificação responsivo")
- Provavelmente o problema está na largura mínima das colunas ou no `min-w` da tabela interna
- Componente Livewire: provavelmente em `/diagnostico` ou componente de verificação de tabelas

---

## Critérios de aceite

- [ ] Cabeçalho "Oficial" visível por completo
- [ ] Nenhum valor numérico truncado nos dois modais
- [ ] Layout funcional em viewport 375px (iPhone SE)
- [ ] Layout funcional em viewport 768px (tablet)
- [ ] Sem quebra visual em desktop

---

## Notas

- Considerar aplicar o mesmo padrão de `card layout` por faixa usado nas tabelas da aba "Tabelas"
- Ou adicionar `min-w` adequado + `overflow-x-auto` no container da tabela do modal
