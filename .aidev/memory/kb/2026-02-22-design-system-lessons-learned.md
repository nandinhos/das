# Lições Aprendidas - Implementação Design System

## Visão Geral
Este documento registra as lições aprendidas durante a implementação do Design System e correções de erros no projeto DAS Calculator.

---

## 1. Docker & Build

### ✅ Prática Correta: Volume para Storage
```yaml
# docker-compose.yml
volumes:
  - das_storage:/var/www/html/storage
```
**Por que**: O SQLite deve estar em volume para persistir dados entre rebuilds.

### ❌ Problema: `.env` ignorado no build
**Sintoma**: `APP_KEY` não encontrada no container  
**Solução**: Copiar `.env.example` para `.env` no entrypoint:
```bash
if [ ! -f "${APP_DIR}/.env" ]; then
    cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
fi
```

### ❌ Problema: SQLite readonly
**Sintoma**: `attempt to write a readonly database`  
**Solução**: 
- Usar `SESSION_DRIVER=file` (não database)
- Usar `CACHE_STORE=file`
- Garantir permissões: `chmod 664` e `chown www-data:www-data`

### ❌ Problema: config:cache ignora variáveis de ambiente
**Solução**: Não usar `php artisan config:cache` em modo desenvolvimento/local. Usar apenas `view:cache` e `route:cache`.

---

## 2. Livewire + Alpine + Vite

### ✅ Prática Correta: app.js
```javascript
// resources/js/app.js
import './bootstrap';

// Esperar evento livewire:init para registrar Alpine data
document.addEventListener('livewire:init', () => {
    Alpine.data('currencyInput', () => ({ ... }));
});
```

**Por que**: Livewire 3/4 injeta Alpine automaticamente. Não usar CDN Alpine separado.

### ❌ Problema: Alpine/Livewire duplicados
**Causa**: CDN Alpine no layout + Livewire bundler conflitando  
**Solução**: Remover `<script src="alpinejs cdn">` do layout - Livewire já gerencia.

### ❌ Problema: $persist property conflict
**Causa**: Múltiplas inicializações do Alpine  
**Solução**: Usar apenas `import 'livewire/livewire'` sem inicialização manual.

### ❌ Problema: x-collapse não funciona
**Causa**: Plugin Collapse não registrado  
**Solução**: Livewire bundler já inclui plugins essenciais. Se necessário, usar `Alpine.plugin()` antes de `Livewire.start()`.

---

## 3. Tailwind CSS & Design System

### ✅ Prática Correta: Cores Customizadas
```css
/* resources/css/app.css */
@theme {
    --color-primary-500: oklch(70.7% 0.165 254.624);
}

.text-primary-500 {
    color: oklch(70.7% 0.165 254.624);
}
```

### ✅ Prática Correta: CSS Variables para Dark Mode
```css
@layer components {
    .das-card {
        background-color: var(--color-das-bg);
    }
}

.dark {
    --color-das-bg: #161615;
}
```

### ✅ Prática Correta: Mobile First
```html
<!-- Touch targets mínimos 44px -->
<button class="min-h-[44px] min-w-[44px]">
<!-- Overflow em tabelas -->
<div class="overflow-x-auto">
```

---

## 4. Componentes Blade

### ✅ Prática Correta: Props Opcionais
```php
// Sempre definir valor padrão ou nullable
public function __construct(
    public ?string $title = null,
    public ?string $actions = null,  // ⚠️ Obrigatório se usado no blade
) {}
```

### ❌ Problema: Undefined variable $actions
**Causa**: Prop usada no blade mas não declarada no componente PHP  
**Solução**: Sempre declarar todas as props usadas nos slots.

---

## 5. Deploy & Entrypoint

### ✅ Prática Correta: entrypoint.sh
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

# 3. Permissões e SQLite
SQLITE_PATH="${APP_DIR}/storage/app/database.sqlite"
touch "${SQLITE_PATH}"
chown -R www-data:www-data "${APP_DIR}/storage"
chmod 664 "${SQLITE_PATH}"

# 4. Migrations + Seeds
php "${APP_DIR}/artisan" migrate --force --no-interaction
php "${APP_DIR}/artisan" db:seed --class=TaxBracketSeeder --force

# 5. Caches (sem config:cache)
php "${APP_DIR}/artisan" view:cache
php "${APP_DIR}/artisan" route:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
```

---

## 6. Configurações Importantes

### .env.example
```env
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/storage/app/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file
```

### config/boost.php
```php
<?php
return [
    'enabled' => env('BOOST_ENABLED', true),
    'browser_logs_watcher' => false,  // ⚠️ Desabilitar em dev
];
```

---

## Checklist Pré-Deploy

- [ ] `.env` configurado corretamente
- [ ] `db:seed` no entrypoint para dados iniciais
- [ ] `SESSION_DRIVER=file` (não database)
- [ ] `CACHE_STORE=file` (não database)
- [ ] Sem CDN Alpine/Livewire no layout
- [ ] Bootstrap Alpine via `livewire:init`
- [ ] Componentes com todas as props declaradas

---

## Referências

- [Livewire 4 Docs](https://livewire.laravel.com/docs)
- [Alpine.js Plugins](https://alpinejs.dev/plugins/collapse)
- [Tailwind CSS 4](https://tailwindcss.com/docs)
- [Laravel Docker](https://laravel.com/docs/deployment)
