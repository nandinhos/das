# Backlog — Sincronização Automática do Fallback (OFFICIAL_BRACKETS)

**Data de criação:** 2026-02-25
**Prioridade:** Média
**Tipo:** Estudo / Arquitetura
**Estimativa:** A definir após estudo

---

## Objetivo

Estudar e definir a melhor estratégia para manter a constante `OFFICIAL_BRACKETS` (fallback hardcoded) sincronizada com os dados oficiais extraídos do Planalto.

---

## Contexto / Motivação

Atualmente, quando o scraper extrai dados com sucesso do Planalto e o Comparador detecta diferenças, o botão "Corrigir Tabelas" atualiza **apenas o banco de dados local**. A constante `OFFICIAL_BRACKETS` no código-fonte permanece desatualizada.

Isso significa que se o banco for resetado ou recriado, os dados de fallback inseridos pela seed serão os antigos, não os corrigidos.

### Diferenças atuais observadas (2026-02-25)

| Faixa | Campo | Fallback (code) | Oficial (Planalto) |
|---|---|---|---|
| 4 | cofins | 14.1 | 13.64 |
| 4 | pis | 3.05 | 2.96 |
| 4 | iss | 31.95 | 32.5 |
| 5 | cofins | 14.42 | 12.82 |
| 5 | pis | 3.13 | 2.78 |
| 5 | iss | 31.55 | 33.5 |

---

## Opções a Estudar

### Opção A — Atualização manual do OFFICIAL_BRACKETS
- Ao detectar diferenças, o dev atualiza manualmente a constante no código
- Simples, mas não escalável

### Opção B — Artisan command para gerar o fallback
- `php artisan scraper:update-fallback`
- Roda o scraper, pega dados oficiais, gera o array PHP formatado
- Dev copia e cola no code, ou o command gera um arquivo

### Opção C — Fallback em arquivo JSON externo
- Mover `OFFICIAL_BRACKETS` de constante PHP para `storage/app/official_brackets.json`
- O botão "Corrigir" atualiza tanto o banco quanto o JSON
- Fallback sempre em sync, sem necessidade de deploy

### Opção D — Fallback dinâmico via banco de dados
- Remover `OFFICIAL_BRACKETS` completamente
- Fallback usa os dados do próprio banco (que já foram corrigidos)
- Simplifica, mas perde a referência hardcoded de segurança

---

## Questões para Discussão

1. O fallback deve servir como **safety net** (dados seguros hardcoded) ou como **referência atualizada**?
2. Se o banco for resetado, qual deve ser o comportamento: inserir dados hardcoded ou tentar scrape online?
3. A seed já usa `OFFICIAL_BRACKETS` — se ela ficar desatualizada, os dados iniciais estarão errados?
4. Vale investir em automação ou a atualização manual é suficiente para a frequência de mudanças legislativas?

---

## Dependências

- `TaxBracketScraperService::OFFICIAL_BRACKETS`
- `TaxBracketSeeder` (usa os dados do fallback para popular o banco)
- Botão "Corrigir Tabelas" no diagnóstico
