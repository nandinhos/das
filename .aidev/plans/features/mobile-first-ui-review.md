# Backlog — Revisão Mobile-First de Todas as Views

**Data de criação:** 2026-02-25
**Prioridade:** Alta
**Tipo:** Frontend / UX / Responsividade
**Agentes responsáveis:** Frontend Agent + Mobile-First Agent
**Estimativa:** ~1 sprint dedicada

---

## Objetivo

Revisão completa de todas as views do sistema para garantir que **100% das páginas e componentes** sejam intuitivos, profissionais e totalmente funcionais em celulares e tablets — não apenas parcialmente adaptados, mas **projetados mobile-first** de forma consistente.

---

## Contexto / Motivação

O sistema já possui implementações parciais de responsividade, mas sem uma revisão sistemática end-to-end o resultado é inconsistente: algumas telas funcionam bem no mobile, outras têm tabelas que transbordam, formulários desconfortáveis de usar em toque, e navegação que não aproveita os padrões modernos de mobile UX.

A meta é elevar o padrão para que **um usuário no celular tenha a mesma qualidade de experiência que no desktop** — especialmente na visualização de tabelas de dados (tabelas tributárias, listagens, relatórios).

---

## Escopo

### Views a revisar (todas)

- [ ] Layout principal / navegação (sidebar, navbar, menu hamburger)
- [ ] Dashboard / home
- [ ] Tabelas tributárias (`tax_brackets`) — listagem e detalhes
- [ ] Versões tributárias (`tax_bracket_versions`)
- [ ] Página de Diagnóstico do Scraper (`/diagnostico`)
- [ ] Formulários (cadastro, edição, filtros)
- [ ] Modais e overlays
- [ ] Paginação e filtros
- [ ] Alertas, badges e status indicators
- [ ] Tabelas com muitas colunas (scroll horizontal vs. card layout)

---

## Critérios de Aceite

### Breakpoints obrigatórios
- [ ] `sm` (640px) — smartphone portrait
- [ ] `md` (768px) — smartphone landscape / tablet portrait
- [ ] `lg` (1024px) — tablet landscape / desktop

### Navegação
- [ ] Menu colapsável (hamburger) funcional e acessível por toque
- [ ] Links e botões com área de toque mínima de 44x44px
- [ ] Breadcrumbs adaptados para mobile

### Tabelas de dados
- [ ] Tabelas com scroll horizontal suave em telas pequenas **ou** transformadas em card-list
- [ ] Colunas prioritárias visíveis no mobile; secundárias colapsáveis
- [ ] Cabeçalhos fixos durante scroll (sticky headers)
- [ ] Ações (editar, excluir) acessíveis por toque (não apenas hover)

### Formulários
- [ ] Inputs com tamanho mínimo para toque confortável
- [ ] Labels posicionadas corretamente (não sobrepostas)
- [ ] Teclado correto ativado (`type="number"`, `type="email"`, etc.)
- [ ] Botões de submit visíveis sem scroll quando possível

### Tipografia e espaçamento
- [ ] Tamanho de fonte legível (`min 14px`) em todos os contextos
- [ ] Espaçamento entre elementos adequado para toque
- [ ] Sem texto cortado ou overflow horizontal

### Performance mobile
- [ ] Imagens e assets otimizados
- [ ] Nenhum componente que cause layout shift em mobile

---

## Approach Técnico

### Filosofia
**Mobile-first**: escrever CSS/Tailwind para o menor breakpoint primeiro, depois expandir para telas maiores — não o contrário.

### Stack de referência
- **Tailwind CSS** — classes responsivas (`sm:`, `md:`, `lg:`)
- **Alpine.js** — interações de menu, modais e collapses
- **Livewire** — garantir que re-renders não quebrem layouts mobile

### Padrão para tabelas pesadas
```
Mobile (< 640px):  Card layout (cada linha vira um card vertical)
Tablet (640-1024px): Tabela compacta com scroll horizontal
Desktop (> 1024px): Tabela completa
```

### Teste de validação
- Usar DevTools (Chrome/Firefox) com presets de dispositivos reais
- Testar: iPhone SE (375px), iPhone 14 (390px), iPad (768px), iPad Pro (1024px)

---

## Notas

- Não redesenhar a identidade visual — apenas adaptar o layout existente
- Priorizar as views de **tabelas tributárias** por serem o core do sistema
- Criar um **checklist de revisão** reutilizável para novas views futuras
- Documentar padrões adotados no KB para consistência futura

---

## Dependências

- Nenhuma dependência de backend
- Pode ser executado em paralelo com outras features

---

*Criado em 2026-02-25 — aguardando priorização*
