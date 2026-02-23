# Regras Frontend TALL Stack - Validadas

## Fontes: Doc Livewire + Doc Alpine.js + Laravel Boost v2

### Reatividade
1. `wire:key` com `md5(json_encode($row))` em todo loop com `x-data`
2. Prefixo unico no key para multiplas tabelas
3. Preferir `$wire.prop` sobre estado Alpine duplicado
4. `$wire.entangle` desencorajado (doc oficial)

### Componentes Livewire
5. Estado no servidor, UI reflete (Boost: "State lives on the server")
6. Validar e autorizar em actions (actions sao como HTTP requests)
7. Alpine apenas para interacoes client-side necessarias

### Estilo e Qualidade
8. `vendor/bin/pint --dirty --format agent` antes de commit
9. Tailwind: usar convencoes existentes do projeto
10. TDD com PHPUnit (nunca Pest)

### Infra
11. Docker multi-stage sem volume = rebuild obrigatorio
12. Se mudanca frontend nao aparece: perguntar sobre npm run build
