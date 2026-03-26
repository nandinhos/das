#!/bin/bash

echo "=== Teste de Deploy - Calculadora DAS ==="
echo ""

# Teste 1: Verificar se os usuários existem
echo "[1/4] Testando criação de usuários..."
if php artisan tinker --execute="echo count(App\Models\User::all());" 2>/dev/null | grep -q "2"; then
    echo "✓ Usuários criados com sucesso"
    php artisan tinker --execute="print_r(App\Models\User::all(['name','email'])->toArray());"
else
    echo "⚠ Usuários não encontrados (pode ser problema de permissão local)"
fi

# Teste 2: Verificar tabelas tributárias
echo ""
echo "[2/4] Testando tabelas tributárias..."
if php artisan tinker --execute="echo count(App\Models\TaxBracket::all());" 2>/dev/null | grep -q "6"; then
    echo "✓ Tabelas tributárias populadas"
    echo "Faixas encontradas:"
    php artisan tinker --execute="print_r(App\Models\TaxBracket::pluck('faixa')->toArray());"
else
    echo "⚠ Tabelas tributárias não encontradas"
fi

# Teste 3: Testar cálculo DAS
echo ""
echo "[3/4] Testando cálculo DAS..."
if php artisan tinker --execute="
    \$service = new App\Services\DasCalculatorService();
    \$revenues = collect([]);
    \$result = \$service->calcular(3, 2024, 10000, \$revenues);
    echo 'R\$ ' . number_format(\$result['valor_total_das'], 2, ',', '.') . PHP_EOL;
" 2>/dev/null | grep -q "R\$"; then
    echo "✓ Cálculo DAS funcionando"
else
    echo "⚠ Cálculo DAS com falha"
fi

# Teste 4: Verificar rotas
echo ""
echo "[4/4] Testando rotas..."
if php artisan route:list --path="login" 2>/dev/null | grep -q "login"; then
    echo "✓ Rotas de login configuradas"
else
    echo "⚠ Rotas não encontradas"
fi

echo ""
echo "=== Verificação Final ==="
echo "Se tudo está OK, o deploy no cPanel deve funcionar."
echo ""
echo "Usuários para login:"
echo "  - Nando Dev (nandinhos@gmail.com) / Aer0G@cembrar"
echo "  - Angelica Domingos (angelica.domingos@hotmail.com) / kinnuty21star"
echo ""
echo "Comandos úteis no cPanel:"
echo "  1. ./deploy.sh  (para rodar o script de deploy completo)"
echo "  2. php artisan migrate:status  (verificar migrations)"
echo "  3. php artisan tinker  (testar dados no banco)"
echo "     > App\Models\User::all(['name','email'])->toArray()"
echo "     > App\Models\TaxBracket::count()"
echo ""