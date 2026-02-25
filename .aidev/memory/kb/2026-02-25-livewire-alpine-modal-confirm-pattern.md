# Lição: Modal Alpine.js como Substituto do `wire:confirm` no Livewire

**Data**: 2026-02-25
**Stack**: Laravel 11 + Livewire 4 + Alpine.js 3 + Tailwind CSS (dark mode)
**Tags**: success-pattern, livewire, alpine-js, ui-pattern, confirmation-modal, blade
**Severidade/Valor**: Padrão de Excelência — reutilizável em qualquer Livewire + dark mode

---

## Contexto

Página de diagnóstico do scraper tributário (`/diagnostico`) com botão "Corrigir Tabelas"
que executa `applyCorrections()` — uma ação destrutiva (atualiza banco de dados).

Era necessário pedir confirmação ao usuário antes de executar.

---

## Problema com `wire:confirm`

### Limitações identificadas
- Usa o `window.confirm()` nativo do browser — sem customização visual
- Não suporta dark mode
- Não permite exibir dados dinâmicos do Blade (ex: `{{ count($differences) }}`)
- Estilo inconsistente com o design system (glassmorphism + Air Force Blue)
- Não suporta transições, animações ou backdrop blur

### Alternativa Descartada
`x-on:click.prevent` + evento customizado: mais complexo e sem benefício adicional

---

## Solução: Modal Alpine.js Embutido

### Padrão Canônico (código de produção)

```blade
{{-- Container pai define o estado Alpine --}}
<div class="flex flex-wrap items-center justify-between gap-3"
     x-data="{ showConfirm: false }">

    {{-- Botão que abre o modal --}}
    <button @click="showConfirm = true"
            class="... bg-emerald-600 ...">
        Corrigir Tabelas
    </button>

    {{-- Modal Overlay --}}
    <div x-show="showConfirm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">

        {{-- Backdrop com blur (clique fecha) --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             @click="showConfirm = false"></div>

        {{-- Caixa do Modal --}}
        <div x-show="showConfirm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md rounded-2xl bg-white dark:bg-[#1a1a1a]
                    border border-slate-200 dark:border-[#3E3E3A] shadow-2xl p-6 space-y-4">

            {{-- Conteúdo com dados dinâmicos do Blade --}}
            <p>
                Atualizar <strong>{{ count($differences) }} campo(s)</strong>?
            </p>

            {{-- Botões --}}
            <div class="flex gap-3">
                <button @click="showConfirm = false">Cancelar</button>

                {{-- Fecha modal E chama Livewire --}}
                <button @click="showConfirm = false; $wire.applyCorrections()">
                    Sim, Corrigir
                </button>
            </div>
        </div>
    </div>
</div>
```

### Por Que Funciona

1. **`x-data` no container pai**: O estado `showConfirm` é compartilhado entre o botão e o modal sem precisar de Alpine global
2. **`fixed inset-0 z-50`**: Overlay cobre todo o viewport sem interferir com Livewire morphing (não está dentro do componente que muda)
3. **Transições duplas**: Backdrop tem transição de opacidade simples; caixa do modal tem opacidade + escala para sensação de "emergir"
4. **`style="display: none"`**: Necessário como fallback CSS para o estado inicial — sem ele, o modal pisca brevemente ao carregar a página (Alpine ainda não inicializou)
5. **`@click="showConfirm = false; $wire.applyCorrections()"`**: Fecha o modal ANTES de chamar o método Livewire — evita que o modal permaneça visível durante o loading

---

## Bônus: Blade Escapa Aspas em Atributos HTML

### Problema
Expressões Alpine com regex ou strings complexas dentro de atributos HTML são corrompidas pelo Blade:
```blade
{{-- ❌ Blade escapa " para &quot; --}}
<div x-data="{ pattern: /\"test\"/g }">
```

### Solução
Usar `<script>` global + `@js()` helper:
```blade
<script>
    window.highlightJson = function(data) {
        return JSON.stringify(data, null, 2)
            .replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*")/g, '<span class="text-emerald-400">$1</span>');
    };
</script>

<div x-data="{ rendered: '' }"
     x-init="rendered = window.highlightJson(@js($data))">
    <pre x-html="rendered"></pre>
</div>
```

---

## Referência de Implementação

| Arquivo | Localização |
|---------|-------------|
| Modal completo | `resources/views/livewire/scraper-diagnostic.blade.php` linhas 252–352 |
| Método Livewire | `app/Livewire/ScraperDiagnostic.php` método `applyCorrections()` |
| JSON highlight global | `resources/views/livewire/scraper-diagnostic.blade.php` tag `<script>` |

---

## Prevenção / Checklist para Reusar

- [ ] Colocar `x-data="{ showModal: false }"` no container que engloba TANTO o botão quanto o modal
- [ ] Sempre adicionar `style="display: none"` no elemento com `x-show` (evita flash no carregamento)
- [ ] Usar `fixed inset-0 z-50` para o overlay (nunca `absolute`, nunca `z-10`)
- [ ] Botão confirmar: `@click="showModal = false; $wire.nomeDoMetodo()"` (fechar primeiro, chamar depois)
- [ ] Nunca usar `wire:confirm` em stack com dark mode customizado ou dados dinâmicos no texto

---

## Commits de Referência

- `55bf079` — Implementação inicial do modal Alpine.js
- `6f54d24` — Badge SINCRONIZADO + backlog atualizado
- `e1200b1` — Refinamentos finais + versionamento tributário
