# Backlog - Fix: Porcentagens das Tabelas Tributárias Multiplicadas por 100 no Frontend

## Visão Geral

As porcentagens exibidas nas tabelas tributárias no frontend estão sendo mostradas multiplicadas por 100 (ex: `8.28%` aparece como `828%`). Investigar a origem do problema e corrigir a exibição.

## Hipóteses Iniciais

1. **Dado já em decimal** - Os valores estão armazenados como decimal (ex: `0.0828`) mas estão sendo exibidos sem conversão (multiplicados por 100 na view/Livewire)
2. **Conversão dupla** - O dado está em percentual (ex: `8.28`) mas está sendo multiplicado por 100 em algum ponto antes da exibição
3. **Formatação JS/Alpine** - Algum filtro ou formatter no frontend está aplicando `* 100` erroneamente

## Escopo da Investigação

- [ ] Verificar como os valores estão armazenados no banco de dados (`tax_brackets`, `simples_nacional_brackets` ou similar)
- [ ] Rastrear o fluxo: Model → Controller/Livewire Component → View/Blade
- [ ] Verificar se há cast no Model (ex: `* 100` em accessor)
- [ ] Verificar formatação no Blade/Alpine.js/Livewire
- [ ] Verificar se a exibição é consistente entre tabelas diferentes (Anexo I, II, III...)

## Critérios de Aceite

- Porcentagens exibidas corretamente no frontend (ex: `8.28%` e não `828%`)
- Sem regressão nos cálculos que dependem desses valores
- Testes cobrindo a exibição correta

## Prioridade

**MEDIUM** — Bug visual, não afeta cálculos internos (a confirmar)

## Estimativa

~60 min (investigação + fix + testes)
