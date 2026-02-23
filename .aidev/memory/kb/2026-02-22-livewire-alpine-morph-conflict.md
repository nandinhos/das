# Licao: Conflito Livewire + Alpine.js no Morph DOM

**Data**: 2026-02-22
**Stack**: TALL (Tailwind, Alpine.js, Livewire 4, Laravel)
**Tags**: bug, frontend, livewire, alpinejs, morph, reatividade

## Contexto

**Stack**: Laravel 11 + Livewire 4 + Alpine.js
**Ambiente**: Docker (Laravel Sail)
**Frequencia**: Sempre (em loops com x-data)
**Impacto**: Alto — dados visuais ficam desatualizados apos re-render

### Sintoma Observado
Elementos com `x-data` dentro de loops `@foreach` do Livewire nao atualizam visualmente apos re-render do componente. Os valores exibidos permanecem com o estado anterior mesmo apos o backend atualizar os dados.

### Comportamento Esperado
Ao re-renderizar o componente Livewire, os valores dentro de `x-data` deveriam refletir os novos dados do servidor.

### Evidencia
Componente `TaxTablesManager` com tabelas de impostos em loop — ao alterar dados no backend, a UI mantinha valores antigos nos elementos Alpine.

## Problema

O morph do Livewire preserva o estado Alpine antigo por design. Quando `x-data="{ val: '{{ $server_value }}' }"` e inicializado, o Alpine guarda esse valor internamente e nao reinicializa durante o morph do DOM.

## Causa Raiz

### Analise (5 Whys)
1. **Por que os dados nao atualizam?** O Alpine preserva o estado do `x-data` durante o morph do DOM.
2. **Por que o Alpine preserva?** O morph do Livewire reutiliza elementos existentes em vez de recria-los, e o Alpine mantem seu estado interno.
3. **Por que o Livewire reutiliza?** Para performance — recriar todo o DOM seria mais lento e perderia estado do usuario (focus, scroll, etc).
4. **Por que nao havia wire:key com hash?** Falta de conhecimento sobre a interacao morph + Alpine x-data em loops.
5. **Por que nao foi detectado antes?** So se manifesta quando dados mudam no backend e precisam refletir em elementos Alpine dentro de loops.

### Causa Raiz Identificada
O mecanismo de morphing do Livewire preserva intencionalmente o estado Alpine (`x-data`) para manter interatividade. Sem um `wire:key` que inclua hash dos dados, o Livewire nao sabe que o elemento precisa ser recriado.

### Tipo de Problema
- [x] Bug de codigo
- [x] Padrao de Codificacao (Sucesso)

## Solucao

### Correcao Aplicada
```blade
{{-- ANTES: wire:key simples, Alpine nao reinicializa --}}
<tr wire:key="row-{{ $item['id'] }}">
    <td x-data="{ val: '{{ $item['value'] }}' }" x-text="val"></td>
</tr>

{{-- DEPOIS: wire:key com hash, forca recriacao do elemento --}}
<tr wire:key="row-{{ $item['id'] }}-{{ md5(json_encode($item)) }}">
    <td x-data="{ val: '{{ $item['value'] }}' }" x-text="val"></td>
</tr>
```

### Por Que Funciona
Quando os dados mudam, o hash `md5(json_encode($item))` muda, alterando o `wire:key`. O Livewire interpreta isso como um elemento novo e o recria completamente, forcando o Alpine a reinicializar o `x-data` com os valores atualizados do servidor.

### Alternativas Consideradas
| Alternativa | Por que nao escolhida |
|-------------|----------------------|
| `$wire.entangle()` | Mais complexo, requer reestruturacao do componente |
| `x-init` com evento | Adiciona complexidade desnecessaria |
| `wire:ignore` | Nao resolve, piora o problema |
| `$refresh` manual | Recarrega todo o componente, perde performance |

### Validacao
- Verificacao manual: dados atualizam corretamente apos re-render
- Confirmado na documentacao oficial do Livewire (morphing + wire:key)
- Confirmado na documentacao oficial do Alpine.js (morph preserva x-data by design)

## Prevencao
Como evitar no futuro:
- [ ] Sempre usar `wire:key` em todo `@foreach` que contenha `x-data`
- [ ] Incluir hash dos dados no `wire:key` quando valores precisam ser reativos
- [ ] Usar prefixo unico no key para multiplas tabelas com mesmo dataset
- [ ] Revisar interacao morph/Alpine ao adicionar `x-data` em loops Livewire

## Referencias
- [Livewire Docs: Morphing](https://livewire.laravel.com/docs/morphing)
- [Alpine.js Docs: Morph Plugin](https://alpinejs.dev/plugins/morph)
- Commit: `56bfea9` — fix(ui): corrige conflito entre Alpine transitions e Livewire
