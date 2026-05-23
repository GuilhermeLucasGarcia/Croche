## Deploy Docker no Render

Este projeto usa Laravel em `backend/` e foi preparado para deploy via Docker no Render com:

- `Dockerfile` na raiz
- `.dockerignore` na raiz
- `render.yaml` com um Web Service Docker
- `docker/start-container.sh` para startup do container

### Como a imagem funciona

- Base de runtime: `php:8.3-cli-alpine`
- Build de dependências PHP: `composer:2`
- Build de assets Vite: `node:22-alpine`
- Porta exposta: `10000`
- Porta real de execução: variável `PORT` do Render
- Healthcheck: `GET /up`

### Variáveis de ambiente obrigatórias no Render

Defina no serviço:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-servico.onrender.com
APP_KEY=base64:SUA_CHAVE_LARAVEL

DB_CONNECTION=pgsql
DB_HOST=seu-host-postgres
DB_PORT=5432
DB_DATABASE=seu-banco
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```

### Variáveis recomendadas

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
LARAVEL_OPTIMIZE=true
RUN_MIGRATIONS=false
```

### Gerar `APP_KEY`

Se você ainda não tiver a chave:

```powershell
php -c .\php.ini backend\artisan key:generate --show
```

Copie o valor completo `base64:...` e configure no Render.

### Build local da imagem

Na raiz do projeto:

```powershell
docker build -t croche-render .
```

### Teste local da imagem

Exemplo com PostgreSQL externo:

```powershell
docker run --rm -p 10000:10000 `
  -e APP_ENV=production `
  -e APP_DEBUG=false `
  -e APP_KEY=base64:SUA_CHAVE_LARAVEL `
  -e APP_URL=http://localhost:10000 `
  -e DB_CONNECTION=pgsql `
  -e DB_HOST=seu-host `
  -e DB_PORT=5432 `
  -e DB_DATABASE=seu-banco `
  -e DB_USERNAME=seu-usuario `
  -e DB_PASSWORD=sua-senha `
  -e SESSION_DRIVER=file `
  -e CACHE_STORE=file `
  -e QUEUE_CONNECTION=sync `
  croche-render
```

Depois abra:

- `http://localhost:10000/`
- `http://localhost:10000/up`

### Deploy no Render

#### Opção 1: Blueprint

1. Suba o repositório com `Dockerfile` e `render.yaml`.
2. No Render, escolha `New +` -> `Blueprint`.
3. Conecte o repositório.
4. Revise o serviço gerado a partir do `render.yaml`.
5. Preencha as variáveis marcadas com `sync: false`.
6. Faça o deploy.

#### Opção 2: Web Service Docker manual

1. No Render, escolha `New +` -> `Web Service`.
2. Conecte o repositório.
3. Selecione `Environment: Docker`.
4. Confirme o `Dockerfile` na raiz.
5. Configure as variáveis de ambiente.
6. Defina o `Health Check Path` como `/up`.
7. Faça o deploy.

### Migrations em produção

O container suporta migrations automáticas no startup com:

```env
RUN_MIGRATIONS=true
```

Use isso apenas se o serviço web puder executar migrations com segurança no seu fluxo de deploy.
Se preferir mais controle, mantenha `RUN_MIGRATIONS=false` e rode migrations separadamente.

### Compatibilidade com o Render

- O container escuta em `0.0.0.0:$PORT`, compatível com Web Service Docker do Render.
- O healthcheck usa `/up`, que já existe no Laravel.
- O build instala dependências PHP e compila assets Vite.
- O log vai para `stderr`, ideal para captura pelo Render.
- O filesystem do Render é efêmero; uploads locais em disco não devem ser tratados como armazenamento permanente.

### Observações importantes

- Se você usar `QUEUE_CONNECTION=database` em produção, crie um Worker Service separado no Render.
- Se você usar `SESSION_DRIVER=database` ou `CACHE_STORE=database`, garanta que as migrations dessas tabelas já foram executadas.
- Para esse projeto, `file` e `sync` simplificam bastante o deploy inicial.

### Validação realizada neste ambiente

Neste ambiente de desenvolvimento, o comando `docker` não está instalado, então não foi possível executar `docker build` e `docker run` aqui.
Mesmo assim, a configuração foi preparada para o runtime do Render e o fluxo de inicialização da aplicação foi validado localmente com o mesmo entrypoint HTTP do container.
