# DRAFT: Upgrade do Agente frontend-responsivo — Padrão de Tabelas em Modais

**Status**: DRAFT — aguardando validação via Context7 + Laravel Boost
**Data de criação**: 2026-02-25
**Origem**: Sprint 2 Mobile-First UI Review (projeto `das`)
**Destino**: `frontend-responsivo.md` no projeto base do orquestrador
**Ticket**: backlog/upgrade-agent-responsivo-table-patterns.md

---

> ⚠️ **NÃO APLICAR** ao agente sem validação Context7 + Laravel Boost.
> Ver backlog para o fluxo de validação antes de atualizar o agente.

---

## Problema Identificado no Agente Atual

O agente `frontend-responsivo.md` atualmente instrui apenas:

```html
{{-- Tabela com scroll horizontal --}}
<div class="overflow-x-auto w-full">
    <table class="...classes-existentes...">
```

**Lacuna**: Não menciona que `min-w-full` (padrão na tabela) **invalida** o `overflow-x-auto` em containers estreitos (modais). O agente corrige o container mas não a classe da tabela — o problema persiste.

**Segundo padrão ausente**: Card layout para tabelas com 4+ colunas em modais — UX superior ao scroll horizontal em mobile.

---

## Mudanças Propostas para o Agente

### Seção a modificar: "Padrões Permitidos" → "Tabela com scroll horizontal"

#### ANTES (atual)
```html
{{-- ENVOLVER a tabela existente sem alterar a tabela em si --}}
<div class="overflow-x-auto w-full">
    <table class="...classes-existentes...">
        ...
    </table>
</div>
```

#### DEPOIS (proposto)
```html
{{-- PADRÃO 1: Scroll horizontal (tabelas simples, ≤ 3 colunas, ou full-page) --}}
{{-- IMPORTANTE: trocar min-w-full por min-w-max quando em container estreito --}}
<div class="overflow-x-auto w-full">
    {{-- min-w-max faz a tabela crescer além do container → ativa o scroll --}}
    {{-- min-w-full limita ao container → scroll nunca ativa em modais --}}
    <table class="min-w-max w-full ...">
        ...
    </table>
</div>

{{-- PADRÃO 2: Card Layout mobile + Tabela desktop (4+ colunas em modais) --}}
{{-- UX superior: sem scroll, dados completamente legíveis --}}

{{-- Cards: visível apenas mobile (< sm = 640px) --}}
<div class="sm:hidden space-y-2">
    @foreach($items as $item)
        <div class="das-card p-3">
            {{-- Cabeçalho do card: identifica a linha da tabela --}}
            <p class="text-xs font-semibold das-text mb-2">
                {{ $item['identificador'] }} — {{ $item['campo'] }}
            </p>
            {{-- Grid 2 colunas para pares de valores --}}
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs das-text-muted mb-0.5">Coluna A</p>
                    <p class="text-sm font-semibold das-text">{{ $item['valor_a'] }}</p>
                </div>
                <div>
                    <p class="text-xs das-text-muted mb-0.5">Coluna B</p>
                    <p class="text-sm font-semibold das-text">{{ $item['valor_b'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabela: visível apenas desktop (>= sm = 640px) --}}
<div class="hidden sm:block overflow-y-auto rounded-lg border ...">
    <table class="min-w-full divide-y ...">
        <thead>...</thead>
        <tbody>...</tbody>
    </table>
</div>
```

---

### Nova seção a adicionar: "Diagnóstico de Tabelas em Containers Estreitos"

```markdown
### Diagnóstico: Tabela em Modal ou Container com max-w-*

**Sintoma**: Colunas/cabeçalhos truncados em mobile mesmo com `overflow-x-auto` no container.

**Causa**: `min-w-full` limita a tabela à largura do container. O overflow nunca é gerado, então o scroll não ativa.

**Árvore de decisão:**

1. O container tem `max-w-*` (modal, painel lateral, card)? → SIM → continua
2. A tabela tem 4+ colunas? → SIM → usar Card Layout (Padrão 2)
3. A tabela tem ≤ 3 colunas? → usar `min-w-max w-full` (Padrão 1)
4. A tabela está em página full-width sem `max-w-*`? → `min-w-full` é correto
```

---

### Nova entrada a adicionar em: "Checklist de Auditoria"

```markdown
[ ] Tabelas dentro de containers com max-w-* (modais, painéis):
    - Verificar `min-w-full` → se presente, avaliar substituição por `min-w-max w-full`
    - Contar colunas: 4+ → Card Layout; ≤ 3 → `min-w-max`
```

---

### Nova entrada a adicionar em: "Anti-Patterns"

```markdown
/* ERRADO: min-w-full em tabela dentro de modal — scroll nunca ativa */
<div class="overflow-x-auto">
    <table class="min-w-full ...">  ← problema silencioso

/* CORRETO: min-w-max permite que a tabela ultrapasse o container */
<div class="overflow-x-auto">
    <table class="min-w-max w-full ...">  ← scroll ativa quando necessário
```

---

## Critérios de Validação (a executar antes de aplicar)

### Via Context7
- [ ] Confirmar que `min-w-max` é suportado e documentado no Tailwind CSS 4
- [ ] Confirmar comportamento de `min-w-max w-full` combinados
- [ ] Verificar se há padrão oficial Tailwind para card layout em modais

### Via Laravel Boost
- [ ] Confirmar que `das-card` e `das-text-muted` são classes globais reutilizáveis
- [ ] Confirmar que o padrão `sm:hidden` + `hidden sm:block` é consistente com as guidelines do projeto
- [ ] Verificar se há conflito com Livewire morphing ao alternar card/table

---

## Referências

- KB local: `.aidev/memory/kb/2026-02-25-mobile-first-tables-modals.md`
- Memória global: `global-standards/lessons/mobile-first/mobile-first-tables-in-modals-tailwind-pattern`
- Implementação: `resources/views/livewire/tax-tables-manager.blade.php` (projeto `das`)
