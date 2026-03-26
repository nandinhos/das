#!/bin/bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo "=== Deploy Script - Calculadora DAS ==="
echo "Diretório: $APP_DIR"

# 1. Configurar .env
echo "[1/8] Configurando ambiente..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✓ .env criado"
else
    echo "✓ .env já existe"
fi

# 2. Gerar APP_KEY se necessário
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null || [ -z "$(grep "^APP_KEY=" .env | cut -d= -f2)" ]; then
    php artisan key:generate --force
    echo "✓ APP_KEY gerado"
else
    echo "✓ APP_KEY já configurado"
fi

# 3. Criar banco SQLite
echo "[2/8] Configurando banco de dados..."
SQLITE_PATH="storage/app/database.sqlite"
mkdir -p storage/app
if [ ! -f "$SQLITE_PATH" ]; then
    touch "$SQLITE_PATH"
    echo "✓ SQLite criado"
else
    echo "✓ SQLite já existe"
fi

# 4. Ajustar permissões CRÍTICO para cPanel
echo "[3/8] Ajustando permissões..."
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data bootstrap/cache storage/logs
chmod -R 775 storage bootstrap/cache
chmod 664 "$SQLITE_PATH" 2>/dev/null || true

# Detectar usuário do cPanel (geralmente o mesmo que roda o script)
if [ -n "${USER:-}" ]; then
    chown -R "$USER":"$USER" storage bootstrap/cache "$SQLITE_PATH" 2>/dev/null || true
fi

echo "✓ Permissões ajustadas"

# 5. Instalar dependências
echo "[4/8] Instalando dependências..."
if [ -f "composer.json" ]; then
    php composer.phar install --no-interaction --optimize-autoloader 2>/dev/null || \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --quiet && \
    php composer.phar install --no-interaction --optimize-autoloader
    echo "✓ Composer dependencies instaladas"
fi

# 6. Build assets
echo "[5/8] Compilando assets..."
if [ -f "package.json" ]; then
    npm ci --prefer-offline 2>/dev/null || npm install 2>/dev/null || echo "⚠ npm não disponível"
    npm run build 2>/dev/null || npx vite build 2>/dev/null || echo "⚠ Build ignorado"
else
    echo "✓ package.json não encontrado"
fi

# 7. Migrations e Seeders
echo "[6/8] Executando migrations..."
php artisan migrate --force --no-interaction
echo "✓ Migrations executadas"

echo "[7/8] Populando dados..."
php artisan db:seed --force --no-interaction
echo "✓ Seeders executados"

# 8. Cache e otimização
echo "[8/8] Otimizando aplicação..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:cache
php artisan route:cache
echo "✓ Cache gerado"

# Permissões finais
chmod 775 storage/framework/sessions 2>/dev/null || true
chmod 664 "$SQLITE_PATH" 2>/dev/null || true

echo ""
echo "=== Deploy Concluído ==="
echo ""
echo "Usuários disponíveis:"
echo "  - Nando Dev (nandinhos@gmail.com)"
echo "  - Angelica Domingos (angelica.domingos@hotmail.com)"
echo ""
