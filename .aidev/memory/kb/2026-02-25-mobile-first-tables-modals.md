# Lição: Tabelas Responsivas em Containers Estreitos (Modais)

**Data**: 2026-02-25
**Stack**: Laravel 12 + Livewire 4 + Tailwind CSS 4 + Alpine.js
**Tags**: success-pattern, mobile-first, responsive, tailwind, tables, modals
**Impacto**: Alto — padrão reutilizável em qualquer modal ou container de largura fixa

---

## Contexto

**Stack**: Laravel 12 + Livewire 4 + Tailwind CSS 4
**Ambiente**: Produção / Mobile (375px–768px)
**Frequência**: Recorrente — todo modal com tabela de 4+ colunas
**Impacto**: Alto — colunas e cabeçalhos truncados em mobile

---

## Problema

Tabelas com 4 colunas dentro de modais (`max-w-lg` = 512px) truncavam colunas e cabeçalhos em mobile (375px). O `overflow-x-auto` no container não ativava scroll porque `min-w-full` limitava a tabela à largura do container — nunca havia overflow.

### Sintoma
- Cabeçalho "Oficial" renderizado como "O"
- Valores como `6.485.000,0000` cortados para `6.485.0`
- `overflow-x-auto` presente mas inativo

---

## Causa Raiz

### Análise (5 Whys)
1. **Por que as colunas foram cortadas?** A tabela nunca ultrapassou a largura do container.
2. **Por que?** `min-w-full` = largura mínima igual à largura do pai — nunca maior.
3. **Por que o overflow não ativou?** Scroll só ativa quando o filho é *maior* que o container.
4. **Por que `min-w-full` foi usado?** Padrão copy-paste de tabelas em páginas full-width onde funciona corretamente.
5. **Causa raiz**: `min-w-full` é correto para páginas full-width mas errado para containers de largura fixa/limitada como modais.

### Tipo
- [x] Padrão de Codificação (Sucesso) — identificação do padrão correto

---

## Solução

### Duas abordagens validadas

#### Abordagem 1: Scroll horizontal (`min-w-max`)
Para tabelas read-only simples (desktop-first com scroll em mobile):

```html
{{-- Container com scroll --}}
<div class="overflow-x-auto rounded-lg border ...">
    {{-- min-w-max: tabela cresce até o tamanho do conteúdo --}}
    {{-- w-full: em desktop ocupa toda a largura disponível --}}
    <table class="min-w-max w-full divide-y ...">
        ...
    </table>
</div>
```

**Por que funciona**: `min-w-max` faz a tabela ter no mínimo a largura do seu conteúdo mais largo. Quando o conteúdo é maior que o container, o `overflow-x-auto` ativa o scroll. `w-full` garante que em telas largas a tabela não fique comprimida.

#### Abordagem 2: Card layout mobile + tabela desktop (preferida para modais)
Para melhor UX mobile — sem scroll, dados legíveis:

```html
{{-- Cards: visível apenas em mobile (< sm = 640px) --}}
<div class="sm:hidden space-y-2">
    @foreach($items as $item)
        <div class="das-card p-3">
            {{-- Cabeçalho do card: identifica a linha --}}
            <p class="text-xs font-semibold das-text mb-2">
                {{ $item['faixa'] }}ª Faixa — {{ $item['field'] }}
            </p>
            {{-- Grid 2 colunas para pares de valores --}}
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs das-text-muted mb-0.5">De</p>
                    <p class="text-sm font-semibold text-red-500">{{ $item['current'] }}</p>
                </div>
                <div>
                    <p class="text-xs das-text-muted mb-0.5">Para</p>
                    <p class="text-sm font-semibold text-green-500">{{ $item['official'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabela: visível apenas em desktop (>= sm = 640px) --}}
<div class="hidden sm:block overflow-y-auto rounded-lg border ...">
    <table class="min-w-full divide-y ...">
        <thead>...</thead>
        <tbody>...</tbody>
    </table>
</div>
```

**Por que funciona**: Mobile recebe cards com labels explícitos, eliminando a necessidade de cabeçalhos de coluna. Desktop mantém a tabela convencional eficiente.

---

## Quando Usar Cada Abordagem

| Cenário | Abordagem | Justificativa |
|---------|-----------|---------------|
| Modal com ≤ 3 colunas | `min-w-max` | Raramente trunca, scroll suficiente |
| Modal com 4+ colunas | Card layout | Melhor UX, sem scroll horizontal |
| Tabela full-page (não modal) | `min-w-full` | Container não limita largura |
| Tabela editável em mobile | Card layout com Alpine | Campos de input não cabem na tabela |
| Tabela read-only, muitos dados | `min-w-max` + scroll | Cards ficariam muito longos |

---

## Padrão das Tabelas Principais (referência)

As tabelas da aba "Tabelas" (editáveis) usam o mesmo card layout com Alpine.js:

```html
{{-- Cards: mobile --}}
<div class="sm:hidden space-y-3 mb-2">
    @foreach($brackets as $index => $row)
        <div class="das-card p-4" wire:key="card-aliq-{{ $row['id'] }}">
            <div class="mb-3">
                <span class="text-sm font-semibold das-text">{{ $row['faixa'] }}ª Faixa</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                {{-- Campo editável com Alpine --}}
                <div x-data="{ editing: false, val: '...' }" @click.away="editing = false">
                    <p class="text-xs font-medium das-text-muted mb-1">Alíquota (%)</p>
                    <span x-show="!editing" @click="editing = true" class="...">...</span>
                    <input x-show="editing" inputmode="decimal" class="w-full ..." />
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabela: desktop --}}
<table class="hidden sm:table min-w-full ...">...</table>
```

---

## Classes Tailwind Chave

| Classe | Contexto | Efeito |
|--------|----------|--------|
| `min-w-full` | Tabela full-page | Largura = container (correto em páginas largas) |
| `min-w-max` | Tabela em modal | Largura = conteúdo mais largo (ativa scroll) |
| `min-w-max w-full` | Tabela em modal | Scroll em mobile + full-width em desktop |
| `sm:hidden` | Wrapper de cards | Oculta em desktop (≥ 640px) |
| `hidden sm:block` | Wrapper de tabela | Oculta em mobile, mostra em desktop |
| `grid grid-cols-2 gap-2` | Card body | 2 colunas de valores lado a lado |
| `das-card` | Card container | Classe do design system (bg/border/shadow) |
| `das-text-muted` | Label do campo | Cor secundária do design system |

---

## Checklist de Revisão (toda tabela em modal)

- [ ] O modal tem `max-w-*` definido? → usar card layout ou `min-w-max`
- [ ] Tabela tem 4+ colunas? → preferir card layout
- [ ] `min-w-full` presente? → substituir por `min-w-max w-full` se em container estreito
- [ ] `overflow-x-auto` no container? → necessário para `min-w-max` funcionar
- [ ] Testado em 375px (iPhone SE)? → critério mínimo de aprovação

---

## Arquivos de Referência

- `resources/views/livewire/tax-tables-manager.blade.php` — implementação dos dois padrões
- `resources/views/livewire/scraper-diagnostic.blade.php` — `min-w-max` em tabela de diagnóstico

---

## Prevenção

- Ao criar qualquer tabela dentro de um modal: checar o checklist acima
- Code review: questionar `min-w-full` em qualquer container com `max-w-*`
- Testar sempre com DevTools em 375px antes de considerar concluído
