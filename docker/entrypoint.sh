#!/bin/sh
set -e

APP_DIR=/var/www/html

# Garante que storage existe (apenas para cache, views, logs)
mkdir -p "${APP_DIR}/storage/framework/sessions" \
    "${APP_DIR}/storage/framework/views" \
    "${APP_DIR}/storage/framework/cache/data" \
    "${APP_DIR}/storage/logs" \
    "${APP_DIR}/bootstrap/cache"

# Ajusta permissões no storage (não mexe no banco!)
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" 2>/dev/null || true

# Descobre pacotes instalados
echo "[entrypoint] Descobrindo pacotes..."
php "${APP_DIR}/artisan" package:discover --ansi

# Executa migrations (isso cria os usuários!)
echo "[entrypoint] Executando migrations..."
php "${APP_DIR}/artisan" migrate --force --no-interaction

# Popula dados iniciais
echo "[entrypoint] Populando dados iniciais..."
php "${APP_DIR}/artisan" db:seed --force --no-interaction

# Gera caches
echo "[entrypoint] Gerando caches..."
php "${APP_DIR}/artisan" view:cache
php "${APP_DIR}/artisan" route:cache

echo "[entrypoint] Iniciando serviços..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
