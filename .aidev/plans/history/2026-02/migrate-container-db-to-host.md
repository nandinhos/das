# Backlog - Migração de Banco de Dados: Volume para Bind Mount

## Visão Geral

Atualmente, o banco de dados SQLite está isolado dentro de um volume nomeado do Docker (`das_storage`). Isso causa inconsistência entre o ambiente local (host) e o ambiente de execução (container). O objetivo é migrar para um *bind mount*, permitindo que o arquivo `.sqlite` seja compartilhado e persistido diretamente na pasta `storage/` do projeto.

## Plano de Ação

### 1. [CRITICAL] Extração de Dados Atuais
**Descrição**: Salvar o banco de dados que já possui tabelas e usuários para não perder informações durante a migração.
- **Comando**: `docker cp calculadora-das:/var/www/html/storage/app/database.sqlite ./storage/app/database.sqlite`

### 2. [HIGH] Alteração do Docker Compose
**Descrição**: Mudar a estratégia de volumes no `docker-compose.yml`.
- **De**: `das_storage:/var/www/html/storage` (Named Volume)
- **Para**: `./storage:/var/www/html/storage` (Bind Mount)
- **Nota**: Remover a definição do volume `das_storage` no final do arquivo.

### 3. [HIGH] Ajuste de Permissões
**Descrição**: Garantir que o usuário do container (nginx/php) consiga escrever no arquivo local.
- **Ação**: `chmod -R 775 storage/` e `chown` se necessário (considerar o WWWUSER do Sail se aplicável).

### 4. [MEDIUM] Validação de Persistência
**Descrição**: Reiniciar os containers e verificar se o Tinker local e o do container enxergam os mesmos dados.

## Critérios de Aceite

- [x] O arquivo `storage/app/database.sqlite` no host é o mesmo usado pelo container.
- [x] O comando `php artisan tinker` retorna os mesmos dados em ambos os ambientes.
- [x] O container inicia sem erros de permissão na pasta `storage`.

## Prioridade

**HIGH** — Essencial para consistência de desenvolvimento e debug.

## Estimativa

~30 min

---
*Criado pelo AI Dev Agent*
Status: Concluído ✅
