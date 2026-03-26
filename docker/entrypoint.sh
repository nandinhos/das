#!/bin/sh
set -e

APP_DIR=/var/www/html

# Garante que storage existe
mkdir -p "${APP_DIR}/storage/framework/sessions" \
    "${APP_DIR}/storage/framework/views" \
    "${APP_DIR}/storage/framework/cache/data" \
    "${APP_DIR}/storage/logs" \
    "${APP_DIR}/storage/app" \
    "${APP_DIR}/bootstrap/cache"

# Corrige DB_DATABASE no .env (força caminho correto)
if grep -q "DB_DATABASE=" "${APP_DIR}/.env" 2>/dev/null; then
    sed -i 's|DB_DATABASE=.*|DB_DATABASE=/var/www/html/storage/app/database.sqlite|' "${APP_DIR}/.env"
else
    echo 'DB_DATABASE=/var/www/html/storage/app/database.sqlite' >> "${APP_DIR}/.env"
fi

# Força SESSION_DRIVER=cookie (mais confiável que file)
if grep -q "SESSION_DRIVER=" "${APP_DIR}/.env" 2>/dev/null; then
    sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=cookie/' "${APP_DIR}/.env"
else
    echo 'SESSION_DRIVER=cookie' >> "${APP_DIR}/.env"
fi

# Gera APP_KEY se não existir ou estiver vazio
if ! grep -q "APP_KEY=base64:" "${APP_DIR}/.env" 2>/dev/null; then
    echo "[entrypoint] Gerando APP_KEY..."
    php "${APP_DIR}/artisan" key:generate --force
fi

# Garante que o arquivo SQLite existe no caminho correto
SQLITE_PATH="${APP_DIR}/storage/app/database.sqlite"
if [ ! -f "${SQLITE_PATH}" ]; then
    echo "[entrypoint] Criando banco de dados SQLite..."
    touch "${SQLITE_PATH}"
fi

# Ajusta permissões (funciona com www-data ou usuário atual)
chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" 2>/dev/null || \
    chown -R "$(id -u):$(id -g)" "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" 2>/dev/null || true

chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chmod 664 "${SQLITE_PATH}" 2>/dev/null || true

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
