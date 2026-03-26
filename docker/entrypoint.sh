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

# DETECTA E USA O BANCO CONFIGURADO NO .env
echo "[entrypoint] Detectando banco de dados..."
DB_PATH=$(grep "^DB_DATABASE=" "${APP_DIR}/.env" | cut -d= -f2)

if [ -z "$DB_PATH" ]; then
    echo "[entrypoint] DB_DATABASE não encontrado no .env, usando padrão"
    DB_PATH="${APP_DIR}/storage/app/database.sqlite"
    # Adiciona ao .env se não existir
    echo "DB_DATABASE=${DB_PATH}" >> "${APP_DIR}/.env"
fi

echo "[entrypoint] Usando banco: ${DB_PATH}"

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

# GARANTE QUE O BANDO EXISTE NO CAMINHO CORRETO
DB_DIR=$(dirname "${DB_PATH}")
if [ ! -d "${DB_DIR}" ]; then
    echo "[entrypoint] Criando diretório do banco: ${DB_DIR}"
    mkdir -p "${DB_DIR}"
fi

if [ ! -f "${DB_PATH}" ]; then
    echo "[entrypoint] Criando banco de dados: ${DB_PATH}"
    touch "${DB_PATH}"
fi

# Ajusta permissões no banco
chown www-data:www-data "${DB_PATH}" 2>/dev/null || \
    chown "$(id -u):$(id -g)" "${DB_PATH}" 2>/dev/null || true
chmod 664 "${DB_PATH}" 2>/dev/null || true

# Ajusta permissões no storage (apenas o necessário para cache/logs)
chown -R www-data:www-data "${APP_DIR}/storage/logs" "${APP_DIR}/storage/framework" 2>/dev/null || \
    chown -R "$(id -u):$(id -g)" "${APP_DIR}/storage/logs" "${APP_DIR}/storage/framework" 2>/dev/null || true
chmod -R 775 "${APP_DIR}/storage/logs" "${APP_DIR}/storage/framework" "${APP_DIR}/bootstrap/cache"

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
