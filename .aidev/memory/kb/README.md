# Base de Conhecimento - Índice

## Categorias e Temas

### Docker & Deploy
**Arquivo**: `2026-02-21-laravel-boost-docker-sail.md`
- Docker com Laravel
- Docker Compose
- Laravel Sail
- Configuração de container

**Arquivo**: `2026-02-24-docker-bind-mount-vs-named-volume.md` ⭐
- Volume nomeado vs Bind Mount para SQLite
- Migração de volume para bind mount
- Permissões storage em WSL2
- `.env` DB_DATABASE: path do container, não do host

### Design System & Frontend
**Arquivo**: `2026-02-22-design-system-lessons-learned.md`
- Docker & Build
- Livewire + Alpine + Vite
- Tailwind CSS
- Componentes Blade
- Deploy & Entrypoint

### Frontend / Reatividade
**Arquivo**: `2026-02-22-livewire-alpine-morph-conflict.md`
- Conflito Livewire morph + Alpine x-data
- wire:key com hash dinâmico
- Reatividade em loops @foreach

**Arquivo**: `2026-02-25-livewire-alpine-modal-confirm-pattern.md` ⭐
- Modal Alpine.js substituindo `wire:confirm` (dark mode + dados dinâmicos)
- Padrão `x-data + x-show + fixed inset-0 z-50` para confirmação destrutiva
- Blade escapa `"` em atributos HTML — usar `window.fn(@js($data))` via `<script>`
- `style="display: none"` obrigatório para evitar flash no carregamento

### Arquitetura Tributária / Data Model
**Arquivo**: `2026-02-24-arquitetura-tributaria-percentual-decimal.md` ⭐
- Convenção TaxBracket=percentual, Calculation=decimal
- DasCalculatorService como fonte única de cálculo
- Bug x100: UPDATE em massa sem backup
- Epsilon 0.01 para comparação de percentuais tributários
- Quando usar migrate:fresh --seed vs UPDATE manual

### Scraping & Integração de Dados Externos
**Arquivo**: `2026-02-24-tax-verification-scraper-comparador.md` ⭐
- Scraping de sites governamentais com fallback hardcoded
- Separação Scraper vs Comparador
- Epsilon para comparação floating-point
- Padrão Livewire + Modal para verificação assíncrona

**Arquivo**: `2026-02-25-scraper-regex-diagnostico-alpine.md`
- DomCrawler vs Regex para HTML governamental ISO-8859-1 (~2MB)
- Merge de duas tabelas HTML por índice (Alíquotas + Repartição Anexo III)
- `mb_convert_encoding` obrigatório para sites do Planalto
- Novas propriedades Livewire requerem rebuild do container Docker

---

## Busca por Tema

| Tema | Arquivo | Seção |
|------|----------|-------|
| APP_KEY, .env | 2026-02-22-design-system-lessons-learned.md | 1. Docker & Build |
| SQLite readonly | 2026-02-22-design-system-lessons-learned.md | 1. Docker & Build |
| Alpine duplicado | 2026-02-22-design-system-lessons-learned.md | 2. Livewire + Alpine |
| x-collapse | 2026-02-22-design-system-lessons-learned.md | 2. Livewire + Alpine |
| Componentes Blade | 2026-02-22-design-system-lessons-learned.md | 4. Componentes Blade |
| entrypoint.sh | 2026-02-22-design-system-lessons-learned.md | 5. Deploy & Entrypoint |
| SESSION_DRIVER | 2026-02-22-design-system-lessons-learned.md | 6. Configurações |
| Mobile First | 2026-02-22-design-system-lessons-learned.md | 3. Tailwind CSS |
| Livewire morph + Alpine | 2026-02-22-livewire-alpine-morph-conflict.md | Causa Raiz |
| wire:key hash | 2026-02-22-livewire-alpine-morph-conflict.md | Solucao |
| x-data em loops | 2026-02-22-livewire-alpine-morph-conflict.md | Prevencao |
| Bind mount SQLite | 2026-02-24-docker-bind-mount-vs-named-volume.md | Solução |
| Volume nomeado Docker | 2026-02-24-docker-bind-mount-vs-named-volume.md | O Problema |
| Permissões storage WSL2 | 2026-02-24-docker-bind-mount-vs-named-volume.md | Procedimento |
| Percentual vs Decimal BD | 2026-02-24-arquitetura-tributaria-percentual-decimal.md | Convenção Definitiva |
| aliquota_nominal 600% bug | 2026-02-24-arquitetura-tributaria-percentual-decimal.md | O Bug |
| DasCalculatorService | 2026-02-24-arquitetura-tributaria-percentual-decimal.md | Regra de Ouro |
| migrate:fresh vs UPDATE | 2026-02-24-arquitetura-tributaria-percentual-decimal.md | Lição 4 |
| Scraper fallback hardcoded | 2026-02-24-tax-verification-scraper-comparador.md | Lição 1 |
| OFFICIAL_BRACKETS | 2026-02-24-tax-verification-scraper-comparador.md | Lição 1 |
| Epsilon tributário 0.01 | 2026-02-24-tax-verification-scraper-comparador.md | Lição 2 |
| Scraper vs Comparador | 2026-02-24-tax-verification-scraper-comparador.md | Lição 4 |
| wire:confirm substituição | 2026-02-25-livewire-alpine-modal-confirm-pattern.md | Problema |
| Modal Alpine.js Livewire | 2026-02-25-livewire-alpine-modal-confirm-pattern.md | Solução |
| x-show + fixed inset-0 | 2026-02-25-livewire-alpine-modal-confirm-pattern.md | Padrão Canônico |
| Blade escapa aspas x-data | 2026-02-25-livewire-alpine-modal-confirm-pattern.md | Bônus |
| $wire.method() Alpine | 2026-02-25-livewire-alpine-modal-confirm-pattern.md | Padrão Canônico |
| DomCrawler vs Regex | 2026-02-25-scraper-regex-diagnostico-alpine.md | Lição 1 |
| ISO-8859-1 Planalto | 2026-02-25-scraper-regex-diagnostico-alpine.md | Lição 3 |
| mb_convert_encoding | 2026-02-25-scraper-regex-diagnostico-alpine.md | Lição 3 |
| Livewire rebuild container | 2026-02-25-scraper-regex-diagnostico-alpine.md | Lição 4 |
| Merge tabelas HTML índice | 2026-02-25-scraper-regex-diagnostico-alpine.md | Lição 2 |

---

## Como Usar

Para buscar lições aprendidas:
```bash
./bin/aidev lessons search --query "<termo>"
```

Ou indique o tema diretamente no prompt:
- "Preciso fazer deploy, tem lição aprendida sobre Docker?"
- "Vou usar Livewire, quais erros evitar?"
