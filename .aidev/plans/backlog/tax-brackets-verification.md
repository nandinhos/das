# Backlog - Verificação de Tabelas Tributárias

## Visão Geral

Sistema de verificação automática das tabelas tributárias do Simples Nacional (Anexo III) comparando os dados locais com a legislação vigente (LC 123/2006).

---

## Tarefas Prioritárias

### 1. [HIGH] Implementar TaxBracketScraperService

**Descrição**: Criar serviço para extrair dados da LC 123/2006 do site do Planalto

**Detalhes técnicos**:
- Usar Http Client do Laravel para fazer request
- Parsear HTML da página oficial (https://www.planoalto.gov.br/ccivil_03/leis/lcp/lcp123.htm)
- Extrair tabelas do Anexo III
- Tratar diferentes cenários de formatação

**Estimativa**: 60 minutos

**Arquivos esperados**:
- `app/Services/TaxBracketScraperService.php`

---

### 2. [HIGH] Implementar TaxBracketComparatorService

**Descrição**: Criar serviço de comparativo entre dados do banco e fonte oficial

**Detalhes técnicos**:
- Receber dados do scraper e do banco
- Comparar: aliquota_nominal, deducao, irpj, csll, cofins, pis, cpp, iss
- Retornar diferenças encontradas
- Marcar status: `uptodate`, `outdated`, `error`

**Estimativa**: 30 minutos

**Arquivos esperados**:
- `app/Services/TaxBracketComparatorService.php`

---

### 3. [HIGH] Criar API de Verificação

**Descrição**: Criar endpoint de API para verificar atualizações

**Rota**: `GET /api/tax-brackets/check`

**Response**:
```json
{
  "status": "uptodate|outdated|error",
  "checked_at": "2026-02-22T15:00:00Z",
  "source": "site_planalto",
  "differences": [
    {
      "faixa": 2,
      "field": "aliquota_nominal",
      "current_value": 11.20,
      "official_value": 12.00,
      "difference": 0.80
    }
  ]
}
```

**Estimativa**: 20 minutos

**Arquivos esperados**:
- `app/Http/Controllers/Api/TaxBracketController.php`
- `routes/api.php`

---

### 4. [MEDIUM] Criar Interface de Verificação

**Descrição**: Adicionar botão na página de tabelas de imposto

**Detalhes técnicos**:
- Adicionar botão "Verificar Atualizações" na view de tax-tables
- Modal para exibir resultado do comparativo
- Indicador visual de status (verde = OK, vermelho = desatualizado)

**Estimativa**: 30 minutos

**Arquivos esperados**:
- `resources/views/livewire/tax-tables-manager.blade.php` (modificar)

---

### 5. [MEDIUM] Configurar Task Agendada

**Descrição**: Verificação automática periódica

**Detalhes técnicos**:
- Criar comando Artisan: `php artisan tax:check-updates`
- Configurar agendamento no Kernel (diário/semanal)
- Opcional: registrar resultado em log/tabela

**Estimativa**: 20 minutos

**Arquivos esperados**:
- `app/Console/Commands/CheckTaxUpdates.php`
- `app/Console/Kernel.php` (modificar)

---

## Dependências

- Laravel Http Client (built-in)
- Extensão PHP cURL
- Livewire (já instalado)

---

## Critérios de Aceitação

1. ✅ Scraping retorna dados formatados corretamente
2. ✅ Comparativo detecta diferenças em qualquer campo
3. ✅ API retorna JSON válido com status correto
4. ✅ Botão funciona e exibe modal com resultado
5. ✅ Task agendada executa sem erros

---

## Observações

- **Fonte primária**: Site do Planalto (LC 123/2006)
- **Escopo inicial**: Apenas Anexo III (prestadores de serviço)
- **Extensibilidade**: Arquitetura permite adicionar Anexo I, II, IV, V posteriormente

---

## Referências

- [LC 123/2006 - Simples Nacional](https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm)
- [Anexo III - Prestadores de Serviços](https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm#art18)
- Documento de lições: `.aidev/memory/kb/2026-02-22-deploy-vps-docker-lessons-learned.md`
