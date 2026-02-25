# Lição: Docker — Bind Mount vs Volume Nomeado para SQLite

**Data**: 2026-02-24
**Stack**: Docker + Laravel + SQLite
**Tags**: docker, sqlite, volume, bind-mount, persistência, debug
**Severidade**: Alta — desenvolvimento inconsistente entre host e container

---

## Contexto

Projeto Laravel com SQLite em container Docker. Inicialmente configurado com
volume nomeado (`das_storage`). O arquivo `.sqlite` ficava isolado dentro do Docker,
inacessível diretamente pelo host para ferramentas como DB Browser, scripts locais
ou tinker sem entrar no container.

---

## O Problema com Volume Nomeado

```yaml
# PROBLEMA: volume nomeado isola o banco
volumes:
  - das_storage:/var/www/html/storage

volumes:
  das_storage:
```

**Consequências:**
- `php artisan tinker` no host via `storage/app/database.sqlite` era um arquivo diferente do container
- Impossível abrir o banco com DB Browser for SQLite no host
- Debug de dados exigia `docker exec calculadora-das ...` para tudo
- Risco de perder dados ao recriar o container sem `docker volume rm`

---

## Solução: Bind Mount

```yaml
# SOLUÇÃO: bind mount — host e container compartilham o mesmo arquivo
volumes:
  - ./storage:/var/www/html/storage
```

O arquivo `./storage/app/database.sqlite` no host **é** o mesmo arquivo usado pelo container.

---

## Procedimento de Migração (Volume → Bind Mount)

### 1. Extrair dados do volume antigo
```bash
docker cp calculadora-das:/var/www/html/storage/app/database.sqlite \
  ./storage/app/database.sqlite
```

### 2. Alterar `docker-compose.yml`
Remover volume nomeado, adicionar bind mount (ver acima).

### 3. Ajustar permissões
```bash
chmod -R 775 storage/
# Se necessário (WSL2 pode não precisar):
# chown -R $USER:www-data storage/
```

### 4. Rebuild e reiniciar
```bash
docker compose down
docker compose up --build -d
```

### 5. Validar
```bash
# Host e container devem retornar os mesmos dados
php artisan tinker --execute="echo \App\Models\User::count();"
docker exec calculadora-das php artisan tinker --execute="echo \App\Models\User::count();"
```

---

## Lições Aprendidas

### 1. SQLite em desenvolvimento: sempre use bind mount
Volumes nomeados fazem sentido para PostgreSQL/MySQL em produção.
Para SQLite em desenvolvimento, bind mount é sempre melhor — você precisa acessar o arquivo.

### 2. `.env` precisa apontar para o path do container, não do host
```env
# CORRETO para bind mount
DB_DATABASE=/var/www/html/storage/app/database.sqlite

# ERRADO (path do host dentro do container)
DB_DATABASE=/home/nandodev/projects/das/storage/app/database.sqlite
```
O container enxerga o arquivo pelo path interno, não pelo path do host.

### 3. Permissões 775 em `storage/` previnem erros de escrita
O PHP/nginx dentro do container roda como `www-data`.
Permissão 775 garante que tanto o owner quanto o grupo podem escrever.

### 4. Extraia o banco ANTES de remover o volume
`docker volume rm` é irreversível. Sempre `docker cp` primeiro.

### 5. Em WSL2, bind mount funciona sem ajuste de UID/GID
O Docker Desktop no WSL2 lida com a tradução de permissões automaticamente.
Não é necessário `chown` extra na maioria dos casos.

---

## Prevenção

- [ ] Novos projetos: sempre iniciar com bind mount para SQLite
- [ ] Verificar `docker-compose.yml` antes de `docker compose down -v` (flag `-v` apaga volumes!)
- [ ] Manter `storage/app/database.sqlite` no `.gitignore` (nunca commitar o banco)
- [ ] Fazer backup periódico: `cp storage/app/database.sqlite storage/app/database.sqlite.bak`

---

## Referências
- Plano: `.aidev/plans/history/2026-02/migrate-container-db-to-host.md`
- `docker-compose.yml` do projeto (bind mount atual)
- Snapshot: `.aidev/SNAPSHOT.md` (seção Infraestrutura)
