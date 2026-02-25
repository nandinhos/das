# Fix: Porcentagens das Tabelas Tributárias Exibidas

**Status:** RESOLVIDO ✓ (arquitetura redefinida em 2026-02-24)
**Data:** 2026-02-24

## Solução Final

Implementada em `../features/fonte-unica-verdade-tributaria.md`.

**Convenção definitiva:**
- `TaxBracket` (BD): **percentual** — `aliquota_nominal=6`, `irpj=4`
- `Calculation` (BD): **decimal** — `aliquota_nominal=0.06`, `irpj_percent=0.04`
- `DasCalculatorService`: divide por 100 ao ler `TaxBracket` para cálculos
- `tax-tables-manager.blade.php`: lê BD diretamente, **sem** `* 100`
- `das-calculator.blade.php`, `calculation-history.blade.php`: recebem decimal, **com** `* 100`
