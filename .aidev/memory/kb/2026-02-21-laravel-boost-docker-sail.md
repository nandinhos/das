# Lição: Configuração e Instalação do Laravel Boost com Docker e Sail

**Data**: 2026-02-21
**Stack**: Laravel 12, Docker (Sail), Composer, Antigravity MCP (Serena, Basic Memory, Context7)
**Tags**: config, integration, deployment, success-pattern

## Contexto
Durante o setup do MCP Laravel Boost no projeto `das` (Calculadora DAS) que está rodando dentro de um container Docker (Laravel Sail modificado), o pacote `headerx/laravel-boost` falhou ao ser encontrado no Composer e também não estava registrando os comandos Artisan mesmo quando instalado, pois o container de produção estava configurado com `APP_ENV=production` e `APP_DEBUG=false`. O objetivo era ter a infraestrutura do AI Dev Agents rodando com acesso total ao banco e contexto interno da aplicação via MCP.

## Problema
1. O comando `composer require headerx/laravel-boost --dev` falhou porque o nome do repositório mudou. 
2. A tentativa de instalar usando `laravel/boost` funcionou no host, no entanto falhou dentro do container porque no `Dockerfile` original a instalação do composer possuía a flag `--no-dev` e as variáveis de ambiente em `docker-compose.yml` desabilitavam pacotes locais (`APP_ENV=production` e `APP_DEBUG=false`), ocultando assim o comando `boost:install`.

### Evidência
```
[2026-02-22 02:40:47] production.ERROR: There are no commands defined in the "boost" namespace.
```

## Causa Raiz

### Análise (5 Whys)
1. **Por que falhou a instalação via artisan?** Porque o comando `boost:install` não estava registrado.
2. **Por que?** Porque o Laravel Boost (em seu ServiceProvider) verifica `APP_ENV` e `APP_DEBUG` e só se registra em ambiente `local` ou no modo de debug ativo. E adicionalmente, as dependências de dev (`--dev`) não estavam sendo copiadas para a imagem.
3. **Por que?** Porque o ambiente Docker estava hardcoded como `production` e `false`, e o Dockerfile usava `--no-dev` para otimizar o tamanho da imagem final.
4. **Por que?** Porque o modelo inicial do app foi preparado apenas para produção.
5. **Por que?** (Causa Raiz) Uma vez que o agente interage com o container da aplicação em ambiente de desenvolvimento local via MCP, faltava adaptar as variáveis de ambiente e o ciclo de build do Composer no Docker para suportar as ferramentas de dev em runtime local, mantendo as credenciais de `WWWUSER` para evitar conflito de permissões e executando a instalação do pacote com `laravel/boost`.

### Tipo de Problema / Padrão
- [x] Configuração incorreta
- [x] Integração de ferramentas AI
- [x] Implantação de container (Docker/Sail)

## Solução

### Correção Aplicada
1. Remoção da flag `--no-dev` do `Dockerfile` temporariamente / condicionalmente para desenvolvimento local.
2. Ajuste do arquivo `docker-compose.yml` na definição das enviromments do app:
```yaml
    environment:
      APP_NAME: "Calculadora DAS"
      APP_ENV: local
      APP_DEBUG: "true"
```
3. Rebuild do container:
```bash
docker compose build das && docker compose up -d das
```
4. Instalação do pacote correto do repositório usando o composer sem o nome antigo e, em seguida, rodar o `boost:install` como root apontando para `www-data` ou para o usuário local de desenvolvimento:
```bash
docker run --rm -v $(pwd):/app -w /app --user $(id -u):$(id -g) composer require laravel/boost --dev
docker exec calculadora-das php artisan boost:install
```

### Por Que Funciona
O Laravel Boost agora é detectado na lista de commands do Artisan, pois as condições para rodá-lo (ambiente local ou debug ativado) são cumpridas juntamente à sua instalação via composer require.

### Solução Alternativa e Padrão Adotado
O próprio AI Dev Agent configurou o Basic Memory `das` e o projeto no Serena MCP usando local paths `/home/nandodev/projects/das`.

## Prevenção
Como evitar no futuro e garantir uma esteira fluida:
- [ ] Construir script de inicialização universal para novos repos onde for detectado Next.js ou Laravel que configure os serviços MCP dinamicamente.
- [ ] Checar sempre os envs de container e Dockerfiles antes de injetar ferramentas de dev CLI.
- [ ] Manter um Backlog de Automação de Infraestrutura AI (ex: Ferramenta de Auto-Setup de Agentes).

## Referências
- [Configuração do Laravel Boost MCP](https://laravel.com/docs/boost)
