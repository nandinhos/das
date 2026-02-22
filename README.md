<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

# Calculadora DAS - Anexo III

Calculadora web para geração do Documento de Arrecadação do Simples Nacional (DAS) para microempresas e empresas de pequeno porte optantes pelo Simples Nacional, especificamente para o **Anexo III** (prestadores de serviços).

##Sobre o Projeto

Esta aplicação foi desenvolvida para automatizar o cálculo do DAS (Imposto Simples Nacional) com base na receita bruta acumulada dos últimos 12 meses, utilizando as aliquotas e deduções vigentes conforme a legislação brasileira.

### Funcionalidades

- **Cálculo Automático**: Calcula o valor do DAS com base na receita bruta
- **Histórico de Cálculos**: Armazena todos os cálculos realizados para consulta
- **Tabela de Impostos**: Faixas de contribuição conforme Anexo III da LC 123/2006
- **Modo Dark**: Interface com suporte a tema escuro
- **Design Responsivo**: Funciona em dispositivos móveis e desktop
- **Persistência de Dados**: Dados armazenados em SQLite

### Tecnologias

- **Backend**: Laravel 12 + PHP 8.4
- **Frontend**: Livewire 4 + Alpine.js + Tailwind CSS 4
- **Database**: SQLite
- **Container**: Docker (PHP-FPM + Nginx)

## Tabela de Impostos - Anexo III

| Faixa | Receita Bruta (12 meses) | Alíquota | Parcela a Deduzir |
|-------|-------------------------|----------|-------------------|
| 1 | Até R$ 180.000,00 | 6,00% | R$ 0,00 |
| 2 | De R$ 180.000,01 a R$ 360.000,00 | 11,20% | R$ 9.360,00 |
| 3 | De R$ 360.000,01 a R$ 720.000,00 | 13,50% | R$ 17.640,00 |
| 4 | De R$ 720.000,01 a R$ 1.800.000,00 | 16,00% | R$ 35.640,00 |
| 5 | De R$ 1.800.000,01 a R$ 3.600.000,00 | 21,00% | R$ 125.640,00 |
| 6 | De R$ 3.600.000,01 a R$ 4.800.000,00 | 33,00% | R$ 648.000,00 |

*Valores vigentes conforme legislação atual (LC 123/2006, LC 155/2016 e regulamentações posteriores)*

## Deploy

### Usando Docker

```bash
# Build da imagem
docker build -t das-app .

# Executar container
docker run -d --name das-app -p 8080:80 das-app
```

A aplicação estará disponível em: `http://localhost:8080`

### Variáveis de Ambiente

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
# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Criar banco
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Compilar assets
npm run build

# Servidor local
php artisan serve
```

## Estrutura do Projeto

```
├── app/
│   ├── Livewire/          # Componentes Livewire
│   └── Services/          # Serviços (DasCalculatorService)
├── database/
│   ├── migrations/        # Migrações do banco
│   └── seeders/          # Seeders (TaxBracketSeeder)
├── resources/
│   ├── css/              # Estilos Tailwind
│   ├── js/               # Scripts JavaScript
│   └── views/            # Views Blade + Livewire
├── docker/               # Configurações Docker
└── storage/               # Arquivos e banco de dados
```

## Licença

MIT License - Copyright (c) 2024 Coral 360 LTDA
