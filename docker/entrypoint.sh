#!/bin/sh
set -e

APP_DIR=/var/www/html

# Copia .env.example para .env se não existir
if [ ! -f "${APP_DIR}/.env" ]; then
    echo "[entrypoint] Copiando .env.example para .env..."
    cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
fi

# Gera APP_KEY se não existir ou estiver vazio
if ! grep -q "APP_KEY=base64:" "${APP_DIR}/.env" 2>/dev/null; then
    echo "[entrypoint] Gerando APP_KEY..."
    php "${APP_DIR}/artisan" key:generate --force
fi

# Garante que o arquivo SQLite existe
SQLITE_PATH="${APP_DIR}/storage/app/database.sqlite"
if [ ! -f "${SQLITE_PATH}" ]; then
    echo "[entrypoint] Criando banco de dados SQLite..."
    touch "${SQLITE_PATH}"
fi
chown www-data:www-data "${SQLITE_PATH}"
chmod 664 "${SQLITE_PATH}"

# Garante permissões corretas no storage (incluindo volume externo)
chown -R www-data:www-data \
    "${APP_DIR}/storage" \
    "${APP_DIR}/bootstrap/cache"

# Garante que o SQLite tem permissões de escrita
chmod 664 "${SQLITE_PATH}" 2>/dev/null || true

# Descobre pacotes instalados (gera bootstrap/cache/services.php e packages.php)
echo "[entrypoint] Descobrindo pacotes..."
php "${APP_DIR}/artisan" package:discover --ansi

# Executa migrations
echo "[entrypoint] Executando migrations..."
php "${APP_DIR}/artisan" migrate --force --no-interaction

# Popula dados iniciais (tabelas tributárias)
echo "[entrypoint] Populando dados iniciais..."
php "${APP_DIR}/artisan" db:seed --class=TaxBracketSeeder --force 2>/dev/null || true

# Gera caches de produção (apenas view cache - config:cache causa problemas)
echo "[entrypoint] Gerando caches..."
rm -f "${APP_DIR}/bootstrap/cache/config.php" 2>/dev/null || true
php "${APP_DIR}/artisan" view:cache
php "${APP_DIR}/artisan" route:cache

# Garante permissões finais no SQLite (após migrations e caches)
chown www-data:www-data "${SQLITE_PATH}"
chmod 664 "${SQLITE_PATH}"

echo "[entrypoint] Iniciando serviços..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
