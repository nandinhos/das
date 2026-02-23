<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

# Calculadora DAS - Anexo III

Calculadora web para geracao do Documento de Arrecadacao do Simples Nacional (DAS) para microempresas e empresas de pequeno porte optantes pelo Simples Nacional, especificamente para o **Anexo III** (prestadores de servicos).

## Sobre o Projeto

Esta aplicacao foi desenvolvida para automatizar o calculo do DAS (Imposto Simples Nacional) com base na receita bruta acumulada dos ultimos 12 meses, utilizando as aliquotas e deducoes vigentes conforme a legislacao brasileira.

### Funcionalidades

- **Calculo Automatico**: Calcula o valor do DAS com base na receita bruta mensal e RBT12
- **Gestao de Receitas**: Cadastro e gerenciamento de receitas mensais
- **Historico de Calculos**: Armazena todos os calculos realizados para consulta futura
- **Tabelas de Impostos Editaveis**: Faixas de contribuicao conforme Anexo III da LC 123/2006 com edicao inline
- **Verificacao de Tabelas**: Comparativo automatico das aliquotas locais com a legislacao vigente (scraping do site do Planalto)
- **Reparticao Tributaria**: Visualizacao da distribuicao por tributo (IRPJ, CSLL, COFINS, PIS, CPP, ISS)
- **Modo Dark**: Interface com suporte a tema escuro
- **Design Responsivo**: Funciona em dispositivos moveis e desktop

### Stack Tecnologica

- **Backend**: Laravel 12 + PHP 8.4
- **Frontend**: Livewire 4 + Alpine.js + Tailwind CSS 4 (TALL Stack)
- **Database**: SQLite
- **Container**: Docker via Laravel Sail (PHP 8.4 + MySQL/SQLite)

## Arquitetura

### Componentes Livewire

| Componente | Responsabilidade |
|-----------|-----------------|
| `DasCalculator` | Calculo do DAS com base na receita e RBT12 |
| `RevenueManager` | CRUD de receitas mensais |
| `TaxTablesManager` | Edicao inline de aliquotas e deducoes com verificacao |
| `CalculationHistory` | Listagem e consulta de calculos anteriores |

### Servicos

| Servico | Responsabilidade |
|---------|-----------------|
| `DasCalculatorService` | Logica de negocio do calculo DAS |
| `TaxBracketScraperService` | Scraping da LC 123/2006 (site do Planalto) |
| `TaxBracketComparatorService` | Comparativo entre dados locais e fonte oficial |

### Modelos

| Modelo | Descricao |
|--------|-----------|
| `TaxBracket` | Faixas tributarias do Anexo III |
| `Revenue` | Receitas mensais cadastradas |
| `Calculation` | Historico de calculos realizados |

### API

| Rota | Metodo | Descricao |
|------|--------|-----------|
| `/` | GET | Pagina principal (Livewire) |
| `/api/tax-brackets/check` | GET | Verificacao de atualizacoes nas tabelas tributarias |

## Tabela de Impostos - Anexo III

| Faixa | Receita Bruta (12 meses) | Aliquota | Parcela a Deduzir |
|-------|-------------------------|----------|-------------------|
| 1 | Ate R$ 180.000,00 | 6,00% | R$ 0,00 |
| 2 | De R$ 180.000,01 a R$ 360.000,00 | 11,20% | R$ 9.360,00 |
| 3 | De R$ 360.000,01 a R$ 720.000,00 | 13,50% | R$ 17.640,00 |
| 4 | De R$ 720.000,01 a R$ 1.800.000,00 | 16,00% | R$ 35.640,00 |
| 5 | De R$ 1.800.000,01 a R$ 3.600.000,00 | 21,00% | R$ 125.640,00 |
| 6 | De R$ 3.600.000,01 a R$ 4.800.000,00 | 33,00% | R$ 648.000,00 |

*Valores vigentes conforme LC 123/2006, LC 155/2016 e regulamentacoes posteriores*

## Deploy

### Usando Docker (Laravel Sail)

```bash
# Subir containers
./vendor/bin/sail up -d

# Rodar migracoes e seed
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

A aplicacao estara disponivel em: `http://localhost:8080`

### Docker standalone

```bash
# Build da imagem
docker build -t das-app .

# Executar container
docker run -d --name das-app -p 8080:80 das-app
```

### Variaveis de Ambiente

```env
APP_NAME=DAS
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/storage/app/database.sqlite
SESSION_DRIVER=file
CACHE_STORE=file
```

## Desenvolvimento

```bash
# Instalar dependencias
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Criar banco e popular
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Compilar assets
npm run build

# Servidor local
php artisan serve
```

### Convencoes

- **Commits**: Em portugues, formato `tipo(escopo): descricao`
- **Testes**: TDD com PHPUnit (nunca Pest)
- **Linter**: Laravel Pint (`vendor/bin/pint --dirty`)
- **Wire:key**: Obrigatorio com hash MD5 em loops `@foreach` com `x-data`

## Estrutura do Projeto

```
app/
├── Http/Controllers/
│   └── Api/
│       └── TaxBracketController.php
├── Livewire/
│   ├── CalculationHistory.php
│   ├── DasCalculator.php
│   ├── RevenueManager.php
│   └── TaxTablesManager.php
├── Models/
│   ├── Calculation.php
│   ├── Revenue.php
│   └── TaxBracket.php
└── Services/
    ├── DasCalculatorService.php
    ├── TaxBracketComparatorService.php
    └── TaxBracketScraperService.php
database/
├── migrations/
└── seeders/
    ├── DatabaseSeeder.php
    └── TaxBracketSeeder.php
resources/views/
├── components/layouts/
├── livewire/
│   ├── calculation-history.blade.php
│   ├── das-calculator.blade.php
│   ├── revenue-manager.blade.php
│   └── tax-tables-manager.blade.php
```

## Licenca

MIT License - Copyright (c) 2024 Coral 360 LTDA
