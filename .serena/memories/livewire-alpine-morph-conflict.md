# Livewire + Alpine.js: Conflito de Estado no Morph DOM

## Regra Critica de Frontend

**Sempre usar `wire:key` com hash dinamico em loops `@foreach` que contenham `x-data` do Alpine.js.**

## Problema
O morph do Livewire preserva estado Alpine `x-data` por design. Valores inicializados com dados do servidor (`{{ $var }}`) nao atualizam apos re-render do componente.

## Solucao Validada (Doc Oficial)
```blade
@foreach($items as $index => $item)
    <tr wire:key="prefixo-{{ $item['id'] }}-{{ md5(json_encode($item)) }}">
        <div x-data="{ val: '{{ $item['valor'] }}' }">
            {{-- Alpine reinicializa porque wire:key mudou --}}
        </div>
    </tr>
@endforeach
```

## Regras
1. `wire:key` obrigatorio em todo `@foreach` com `x-data`
2. Hash dos dados no key (`md5(json_encode($row))`) para reatividade
3. Prefixo unico no key quando mesmo dataset em multiplas tabelas
4. Docker multi-stage sem volume mount = rebuild obrigatorio

## Alternativas
- `$wire.propriedade` direto (sem estado Alpine separado)
- `@evento.window` para escutar mudancas via evento Livewire

## Validacao
- Livewire docs: morph preserva estado, wire:key forca recriacao
- Alpine.js docs: morph preserva x-data by design
