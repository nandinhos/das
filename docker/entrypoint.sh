#!/bin/sh
set -e

APP_DIR=/var/www/html

# Garante que o arquivo SQLite existe
SQLITE_PATH="${APP_DIR}/storage/app/database.sqlite"
if [ ! -f "${SQLITE_PATH}" ]; then
    echo "[entrypoint] Criando banco de dados SQLite..."
    touch "${SQLITE_PATH}"
fi
chown www-data:www-data "${SQLITE_PATH}"
chmod 664 "${SQLITE_PATH}"

# Garante permissões corretas no storage
chown -R www-data:www-data \
    "${APP_DIR}/storage" \
    "${APP_DIR}/bootstrap/cache"

# Descobre pacotes instalados (gera bootstrap/cache/services.php e packages.php)
echo "[entrypoint] Descobrindo pacotes..."
php "${APP_DIR}/artisan" package:discover --ansi

# Executa migrations
echo "[entrypoint] Executando migrations..."
php "${APP_DIR}/artisan" migrate --force --no-interaction

# Gera caches de produção
echo "[entrypoint] Gerando caches..."
php "${APP_DIR}/artisan" config:cache
php "${APP_DIR}/artisan" route:cache
php "${APP_DIR}/artisan" view:cache

echo "[entrypoint] Iniciando serviços..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
