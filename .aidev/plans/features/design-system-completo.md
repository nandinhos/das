# Design System Completo - UI/UX Padronizado

## 📋 Descrição
Criar e aplicar um design system completo no projeto DAS Calculator, com:
- Substituição da cor indigo por blue (oklch 70.7% 0.165 254.624)
- Componentes Blade reutilizáveis
- Padronização total de estilos
- Implementação Mobile First
- Dark mode consistente

## 🎯 Escopo

### Etapa 1: Configuração + Componentes Base
- [ ] Atualizar app.css com blue oklch + design tokens
- [ ] Criar das/button.blade.php (primary, secondary, danger, ghost)
- [ ] Criar das/card.blade.php (wrapper com slots)
- [ ] Criar das/input.blade.php (text, currency)
- [ ] Criar das/select.blade.php (dropdown)
- [ ] Aplicar em Revenue Manager

### Etapa 2: Componentes de Exibição
- [ ] Criar das/badge.blade.php
- [ ] Criar das/table-wrapper.blade.php
- [ ] Criar das/table.blade.php
- [ ] Criar das/empty-state.blade.php
- [ ] Aplicar em DAS Calculator

### Etapa 3: Componentes de Layout
- [ ] Criar das/section.blade.php
- [ ] Criar das/modal.blade.php
- [ ] Criar das/page-header.blade.php
- [ ] Aplicar em Calculation History

### Etapa 4: Consolidação
- [ ] Aplicar em Tax Tables Manager
- [ ] Atualizar Layout Principal (app.blade.php)
- [ ] Validação Mobile First
- [ ] Validação Dark Mode

## 📦 Entregáveis
- 11 componentes Blade em resources/views/components/das/
- 5 views refatoradas
- CSS tokens centralizados

## 🔗 Dependências
- Laravel 12
- Livewire 4
- Tailwind CSS 4
- Alpine.js

## 📅 Estimativa
4 ciclos iterativos (1 ciclo por etapa)

## Status: Planejado
