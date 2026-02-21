# =============================================================================
# Stage 1: Dependências PHP (Composer) — gera o vendor/ sem build context grande
# =============================================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --ignore-platform-reqs

# =============================================================================
# Stage 2: Build dos assets (Vite + Tailwind + Livewire ESM)
# =============================================================================
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json ./
RUN npm install --prefer-offline

COPY vite.config.js       ./
COPY resources/css        resources/css
COPY resources/js         resources/js
COPY resources/views      resources/views
COPY app/Livewire         app/Livewire
COPY public               public

# livewire.esm.js necessário para Vite resolver o import em app.js
COPY --from=vendor /app/vendor/livewire/livewire/dist/livewire.esm.js \
     vendor/livewire/livewire/dist/livewire.esm.js

# Views de paginação do Laravel (escaneadas pelo Tailwind via @source em app.css)
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
     vendor/laravel/framework/src/Illuminate/Pagination/resources/views

RUN npm run build

# =============================================================================
# Stage 3: Aplicação PHP + nginx (imagem final)
# =============================================================================
FROM php:8.4-fpm-alpine AS app

# Dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev

# Extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_sqlite \
        mbstring \
        zip \
        gd \
        bcmath \
        intl \
        opcache

# Configuração OPcache para produção
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copia o código da aplicação (sem .env, vendor, node_modules, public/build)
COPY . .

# Usa o vendor gerado no Stage 1 (evita instalar Composer na imagem final)
COPY --from=vendor /app/vendor vendor/

# Copia assets compilados do Stage 2
COPY --from=assets /app/public/build public/build

# Cria diretórios necessários e ajusta permissões
RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
 && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
 && chmod -R 775 \
        storage \
        bootstrap/cache

# Configuração nginx
RUN rm -f /etc/nginx/http.d/default.conf
COPY docker/nginx.conf       /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh    /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
