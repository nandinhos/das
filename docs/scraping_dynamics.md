# Documentação Técnica: Dinâmica de Scraping do Simples Nacional

Este documento detalha a engenharia por trás do sistema de captura automática das tabelas de alíquotas do Simples Nacional (Anexo III) diretamente da fonte oficial (Planalto).

## 1. Objetivo e Desafios
O objetivo é extrair dados atualizados da **Lei Complementar nº 123 (Art. 18)**. 

### Desafios Técnicos:
*   **Volume de Dados:** A página oficial ultrapassa 1.6MB de HTML puro, o que pode causar timeouts em conexões instáveis.
*   **Encoding:** O site do Planalto utiliza `ISO-8859-1`, exigindo conversão para `UTF-8` para evitar corrupção de caracteres.
*   **Estrutura Volátil:** As tabelas no HTML não possuem IDs ou classes semânticas consistentes, exigindo uma abordagem baseada em heurística.

## 2. Arquitetura da Solução

A solução baseia-se no `TaxBracketScraperService.php`, utilizando o `Symfony DomCrawler` para navegação no DOM.

### Estratégia de Conexão (Resiliência)
```php
Http::withHeaders([
    'User-Agent' => 'Mozilla/5.0 ...' // Emulação de navegador real
])
->timeout(90) // Tempo estendido para páginas pesadas
->retry(2, 500) // Mecanismo de re-tentativa em caso de falha momentânea
```

### Dinâmica de Identificação da Tabela
Como não há identificadores únicos, o scraper percorre todas as tabelas (`<table>`) e aplica os seguintes critérios:
1.  **Filtro de Palavras-Chave:** Converte o texto da tabela para minúsculas e verifica a presença de "alíquota" (ou "aliquota") e "deduzir".
2.  **Validação de Estrutura:** A tabela deve possuir pelo menos 3 colunas (Faixa, Alíquota, Valor a Deduzir).
3.  **Análise de Conteúdo:** O scraper tenta extrair a primeira alíquota nominal. Se for aproximadamente `6%` (alíquota inicial do Anexo III), a tabela é marcada como válida.

### Normalização de Dados
Os dados brutos do governo (ex: `11,20%` ou `R$ 9.360,00`) são processados para um formato numérico padrão:
*   Remoção de símbolos monetários e percentuais via Regex.
*   Conversão do padrão brasileiro (vírgula como decimal) para o padrão computacional (ponto).
*   **Escala:** Alíquotas nominais são convertidas para decimais (ex: `6.0` torna-se `0.06`) para facilitar cálculos matemáticos diretos.

## 3. Fluxo de Verificação e Comparação

O sistema não apenas captura, mas também valida a integridade dos dados locais:

1.  **Comparator (`TaxBracketComparatorService`):** Recebe os dados "oficiais" (scraped) e os compara com os "locais" (banco de dados).
2.  **Tolerância de Precisão:** Utiliza uma margem de `0.0001` para evitar falsos positivos decorrentes de arredondamentos de ponto flutuante.
3.  **Interface de Gerenciamento (`TaxTablesManager`):** Permite que o usuário visualize as discrepâncias antes de confirmar a atualização (Single Source of Truth).

## 4. Monitoramento Automático
O comando CLI `CheckTaxUpdates.php` pode ser agendado via Cron para realizar essa verificação periodicamente, enviando logs ou alertas caso a legislação sofra alterações que precisem de intervenção manual ou automática.

> [!IMPORTANT]
> A dinâmica foi projetada para ser defensiva. Se o scraper não encontrar exatamente 6 faixas ou se os valores iniciais forem divergentes do esperado para o Anexo III, o processo é abortado para evitar corrupção da tabela tributária.
