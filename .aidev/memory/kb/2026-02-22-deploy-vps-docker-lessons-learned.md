# Lições Aprendidas - Deploy VPS com Docker

## Visão Geral
Este documento registra as lições aprendidas durante o deploy do projeto DAS Calculator na VPS usando Docker, incluindo erros encontrados e suas correções.

---

## 1. Configuração Inicial do Projeto

### ✅ Prática Correta: Setup Laravel Local
```bash
# 1. Copiar .env.example para .env
cp .env.example .env

# 2. Editar configurações importantes
APP_NAME=DAS
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/home/devuser/projects/das/database/database.sqlite

# 3. Instalar dependências
composer install
php artisan key:generate

# 4. Criar banco e migrations
touch database/database.sqlite
php artisan migrate --force

# 5. Seeds
php artisan db:seed --force

# 6. Assets
npm install
npm run build
```

---

## 2. Problemas Encontrados no Deploy

### ❌ Problema 1: Porta 80 Ocupada pelo Apache
**Sintoma**: `php artisan serve` não conseguia usar porta 80  
**Causa**: Apache2 estava rodando na porta 80  
**Solução**: Usar Docker em porta alternativa (8080) - não precisa parar o Apache

### ❌ Problema 2: Apache Mostrando Código PHP
**Sintoma**: Página exibia código PHP ao invés de executar  
```
<?php
use Illuminate\Foundation\Application;
...
```
**Causa**: Módulo PHP não habilitado no Apache  
**Solução**: Instalar módulo PHP:
```bash
sudo apt install libapache2-mod-php8.3
sudo a2enmod php8.3
sudo systemctl restart apache2
```

### ❌ Problema 3: Forbidden - Permissão Negada
**Sintoma**: `403 Forbidden - You don't have permission to access this resource`  
**Causa**: Diretório home do usuário (/home/devuser) tinha permissão 700  
**Solução**: Adicionar permissão de execução para outros usuários:
```bash
sudo chmod o+x /home/devuser
sudo chmod o+x /home/devuser/projects
```

### ❌ Problema 4: Decisão - Isolar com Docker
**Conclusão**: Problemas de permissão e configuração do Apache eram complicados  
**Solução**: Usar Docker para isolar completamente o ambiente

---

## 3. Docker - Build e Deploy

### ✅ Build da Imagem
```bash
docker build -t das-app .
```

### ✅ Executar Container
```bash
docker run -d --name das-app -p 8080:80 das-app
```

### ✅ Verificar Status
```bash
docker logs das-app
docker ps
curl http://localhost:8080
```

---

## 4. Configuração do Dockerfile

### Estrutura do Dockerfile (Multi-stage)
```dockerfile
# Stage 1: Dependências PHP (Composer)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --no-progress --no-scripts --ignore-platform-reqs

# Stage 2: Build dos assets (Vite)
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json ./
RUN npm install --prefer-offline
# ... copiar arquivos necessários
RUN npm run build

# Stage 3: Aplicação PHP + nginx
FROM php:8.4-fpm-alpine AS app
RUN apk add --no-cache nginx supervisor sqlite-dev ...
RUN docker-php-ext-install pdo pdo_sqlite mbstring zip gd bcmath intl opcache
COPY . .
COPY --from=vendor /app/vendor vendor/
COPY --from=assets /app/public/build public/build
RUN mkdir -p storage/... bootstrap/cache && chown -R www-data:www-data ...
```

---

## 5. Entrypoint.sh - Automação

### ✅ Script de Entrada
```bash
#!/bin/sh
set -e
APP_DIR=/var/www/html

# 1. Setup .env
if [ ! -f "${APP_DIR}/.env" ]; then
    cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
fi

# 2. Gerar APP_KEY
if ! grep -q "APP_KEY=base64:" "${APP_DIR}/.env"; then
    php "${APP_DIR}/artisan" key:generate --force
fi

# 3. Criar diretórios e permissões
mkdir -p "${APP_DIR}/storage/app/public"
mkdir -p "${APP_DIR}/storage/framework/cache/data"
mkdir -p "${APP_DIR}/storage/framework/sessions"
mkdir -p "${APP_DIR}/storage/framework/views"
mkdir -p "${APP_DIR}/storage/logs"
mkdir -p "${APP_DIR}/bootstrap/cache"
chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"

# 4. Migrations
php "${APP_DIR}/artisan" migrate --force --no-interaction

# 5. Seeds
php "${APP_DIR}/artisan" db:seed --force --no-interaction

# 6. Caches
php "${APP_DIR}/artisan" view:cache
php "${APP_DIR}/artisan" route:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
```

---

## 6. Nginx no Container

### ✅ Configuração nginx.conf
```nginx
server {
    listen 80;
    index index.php index.html;
    root /var/www/html/public;
    
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location /build {
        alias /var/www/html/public/build;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

---

## 7. Comandos Úteis

### Docker
```bash
# Build
docker build -t das-app .

# Run
docker run -d --name das-app -p 8080:80 das-app

# Logs
docker logs -f das-app

# Restart
docker restart das-app

# Stop/Start
docker stop das-app
docker start das-app

# Remove
docker rm das-app
```

### Verificação
```bash
# Testar localmente
curl http://localhost:8080

# Ver processos
docker ps

# Ver logs em tempo real
docker logs -f das-app
```

---

## 8. Checklist Deploy

- [x] Projeto clonado
- [x] Dependências Composer instaladas
- [x] .env configurado (APP_KEY, DB)
- [x] Migrations executadas
- [x] Seeds executadas
- [x] Assets compilados (npm run build)
- [x] Docker build executado
- [x] Container rodando na porta 8080
- [x] Aplicação acessível

---

## 9. Lições Aprendidas

1. **Usar Docker para isolar** - Resolve problemas de permissão e dependências do sistema host

2. **Porta 8080** -避腐蚀冲突 (evitar conflitos) - usa Docker em porta alternativa

3. **Permissões Linux** - Diretórios pai precisam de `o+x` para Apache acessar

4. **Módulo PHP no Apache** - Precisa instalar `libapache2-mod-php8.3` se usar Apache

5. **Entrypoint.sh** - Automatiza .env, APP_KEY, migrations e seeds

6. **Supervisor** - Gerencia nginx e php-fpm simultaneamente

7. **Multi-stage Build** - Otimiza tamanho da imagem (Composer + Assets separados)

---

## Referências

- [Docker Multi-stage Builds](https://docs.docker.com/build/building/multi-stage/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [PHP Alpine Docker](https://github.com/docker-library/php)
- [Supervisor Config](http://supervisord.org/)
