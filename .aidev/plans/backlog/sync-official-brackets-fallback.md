# Backlog — Versionamento de Tabelas Tributárias

**Data de criação:** 2026-02-25
**Prioridade:** Alta
**Tipo:** Arquitetura / Data Management
**Estimativa:** ~4h

---

## Objetivo

Substituir a constante `OFFICIAL_BRACKETS` hardcoded por um sistema de versionamento de tabelas tributárias com snapshots JSON, permitindo rastreabilidade e correção automática via botão "Corrigir".

---

## Decisão Arquitetural

### Remover `OFFICIAL_BRACKETS` como constante fixa
A constante hardcoded no PHP é difícil de manter e não acompanha atualizações. Será substituída por dados versionados.

### Criar tabela `tax_bracket_versions`
```
tax_bracket_versions
├── id
├── version (int, auto-increment)
├── source (enum: 'seed', 'scraper', 'manual')
├── payload (JSON — array completo das 6 faixas)
├── checksum (SHA-256 do payload normalizado)
├── applied_at (timestamp — quando foi aplicado ao banco)
├── created_at
└── updated_at
```

### Snapshot inicial em `database/seeders/data/`
- Arquivo: `database/seeders/data/tax_brackets_v1.json`
- Contém os dados oficiais vigentes no formato do modelo
- `TaxBracketSeeder` lê do JSON ao invés da constante PHP
- Permite atualizar os dados sem alterar código PHP

### Botão "Corrigir" cria nova versão
Quando o usuário clica "Corrigir Tabelas" no diagnóstico:
1. Atualiza os `TaxBracket` no banco (já implementado)
2. Cria registro em `tax_bracket_versions` com os dados oficiais
3. Gera checksum do payload

### Comparador calcula checksum
- Hash SHA-256 do payload normalizado (JSON serializado e ordenado)
- Compara checksum local vs checksum do scraping
- Se iguais → "SINCRONIZADO" (sem comparação campo a campo)
- Se diferentes → comparação detalhada atual

---

## Arquivos a Criar

| Arquivo | Descrição |
|---|---|
| `database/migrations/xxx_create_tax_bracket_versions_table.php` | Migration da nova tabela |
| `app/Models/TaxBracketVersion.php` | Model Eloquent |
| `database/seeders/data/tax_brackets_v1.json` | Snapshot inicial |

## Arquivos a Modificar

| Arquivo | Mudança |
|---|---|
| `app/Services/TaxBracketScraperService.php` | Remover `OFFICIAL_BRACKETS`, fallback lê do JSON ou última versão |
| `app/Services/TaxBracketComparatorService.php` | Adicionar lógica de checksum |
| `app/Livewire/ScraperDiagnostic.php` | `applyCorrections()` cria nova versão |
| `database/seeders/TaxBracketSeeder.php` | Ler do JSON ao invés da constante |

---

## Fluxo Proposto

```
Seed → Lê tax_brackets_v1.json → Popula banco + cria version v1
                                    ↓
Diagnóstico → Scraper busca Planalto → Comparador calcula checksum
                                    ↓
                            Checksums diferentes?
                            ├── Não → "SINCRONIZADO"
                            └── Sim → Mostra diferenças + botão Corrigir
                                        ↓
                                Clique "Corrigir"
                                ├── Atualiza TaxBracket
                                ├── Cria TaxBracketVersion (v2, source: scraper)
                                └── Atualiza snapshot JSON (opcional)
```

---

## Critérios de Aceite

- [ ] Tabela `tax_bracket_versions` criada e funcional
- [ ] Constante `OFFICIAL_BRACKETS` removida do código
- [ ] Snapshot JSON em `database/seeders/data/`
- [ ] Seeder lê do JSON
- [ ] Botão "Corrigir" registra nova versão no banco
- [ ] Comparador usa checksum para detecção rápida
- [ ] Histórico de versões acessível (query simples)
