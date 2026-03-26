---
title: DEPLOY_GUIDE
type: note
permalink: das/deploy-guide
---

# Deploy Guia - Calculadora DAS

## Status do Projeto ✅

O projeto está pronto para deploy automático no cPanel com:

### ✅ Configurações Corrigidas
- **SESSION_DRIVER=cookie** (funciona sem Redis/database)
- **DB_DATABASE=/var/www/html/storage/app/database.sqlite** (caminho correto)
- **Permissões automáticas** ajustadas no entrypoint.sh
- **APP_KEY** gerado automaticamente

### ✅ Dados Iniciais
- **2 usuários criados automaticamente**:
  - Nando Dev (nandinhos@gmail.com) / Aer0G@cembrar
  - Angelica Domingos (angelica.domingos@hotmail.com) / kinnuty21star
- **6 faixas tributárias populadas** (Anexo III Simples Nacional)

### ✅ Migrações
- `2026_03_26_125438_seed_initial_users` - Cria usuários
- `2026_03_26_140000_populate_tax_brackets` - Popula tabelas tributárias

## Deploy no cPanel

### Opção 1: Deploy Automático (Recomendado)
Se seu cPanel roda o deploy script automaticamente, ele usará o novo `entrypoint.sh` que:

1. ✅ Corrige `DB_DATABASE` automaticamente
2. ✅ Define `SESSION_DRIVER=cookie`
3. ✅ Cria banco SQLite no caminho correto
4. ✅ Ajusta permissões
5. ✅ Roda migrations (incluindo usuários)
6. ✅ Roda seeder (tabelas tributárias)

### Opção 2: Manual (se necessário)
```bash
# No terminal do cPanel:
./deploy.sh

# Ou passo a passo:
php artisan migrate --seed
```

## Verificação Pós-Deploy

Acesse o site e teste:

### 1. Login
```
URL: https://das.fssdev.com.br/login
Usuário: nandinhos@gmail.com
Senha: Aer0G@cembrar
```

### 2. Verificar Dados (via tinker)
```bash
php artisan tinker
# Testar usuários
App\Models\User::all(['name','email'])->toArray()

# Testar tabelas tributárias
App\Models\TaxBracket::count()  # Deve retornar 6

# Testar cálculo
$service = new App\Services\DasCalculatorService();
$result = $service->calcular(3, 2024, 10000, collect([]));
echo $result['valor_total_das']
```

### 3. Funcionalidade Completa
- Login/logout
- Cadastro de receitas
- Cálculo de DAS
- Histórico de cálculos
- Gestão de tabelas tributárias

## Problemas Comuns

### ❌ Não loga
- Verifique se SESSION_DRIVER está como `cookie`
- Cheque se os usuários foram criados: `php artisan tinker -> App\Models\User::all()`

### ❌ Erro de banco
- Verifique caminho do SQLite: `cat .env | grep DB_DATABASE`
- Execute: `php artisan migrate:fresh --seed`

### ❌ Permissões
- Pasta storage deve ter permissão 775
- Arquivo SQLite deve ter 664

## Comandos Úteis para Debug

```bash
# Ver migrations
php artisan migrate:status

# Recriar tudo
php artisan migrate:fresh --seed

# Testar cálculo manual
php artisan tinker
$service = new App\Services\DasCalculatorService();
$service->calcular(3, 2024, 10000, collect([]))

# Ver logs
tail storage/logs/laravel.log
```

## Próximos Passos

1. **Faça o deploy** no cPanel
2. **Teste o login** com as credenciais fornecidas
3. **Verifique o cálculo** DAS
4. **Cadastre algumas receitas** e teste o cálculo completo

Se encontrar problemas, verifique os logs com `tail storage/logs/laravel.log` e execute os comandos de debug acima.

---
**Status:** ✅ PRONTO PARA DEPLOY
**Último commit:** 67d3026 - fix: cria migration para popular tabelas tributárias automaticamente