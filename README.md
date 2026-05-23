## Crochê E-commerce (Laravel)

Este repositório vai conter um e-commerce de crochê feito em PHP (Laravel), com:

- Catálogo (produtos + categorias)
- Carrinho
- Checkout (criação de pedido)
- Área administrativa (gestão de produtos/categorias/pedidos)
- Home baseada no layout enviado (SVG/print)

### Pré-requisitos (Windows)

- PHP 8.2+ (recomendado 8.3)
- Composer 2
- Node.js (já instalado)

### Instalação sugerida via winget

Instale PHP:

```powershell
winget install --id PHP.PHP.8.3 --source winget --accept-source-agreements --accept-package-agreements
```

Se o comando `php` não aparecer imediatamente, feche e reabra o Trae/terminal.

Composer (neste projeto)

- O catálogo do winget nem sempre traz o pacote do Composer neste ambiente.
- Por isso, este repositório usa o Composer como `composer.phar` (na raiz).

Baixar o Composer:

```powershell
$ProgressPreference='SilentlyContinue'
Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile ".\composer.phar"
```

Feche e reabra o terminal e valide:

```powershell
php -v
php .\composer.phar -V
```

### Configuração do PHP (extensões)

Este repositório inclui um `php.ini` na raiz para habilitar extensões necessárias (openssl, sqlite, zip).
Use `php -c .\php.ini ...` ao rodar comandos do Laravel/Composer.

### Criar o projeto Laravel (quando PHP+Composer estiverem ok)

Na raiz deste repositório:

```powershell
php .\composer.phar create-project laravel/laravel backend
```

Depois:

```powershell
copy backend\.env.example backend\.env
php -c .\php.ini backend\artisan key:generate
```

### Banco de dados (Supabase / PostgreSQL)

O projeto está configurado para conectar em um banco PostgreSQL existente no Supabase.

No arquivo `.env` (dentro de `backend/`), ajuste as credenciais do seu Supabase:

```
DB_CONNECTION=pgsql
DB_HOST=aws-0-sa-east-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=sua-senha-do-supabase
```

Não é necessário rodar `php artisan migrate`, pois o esquema já existe no banco.

### Rodar localmente

```powershell
php -c .\php.ini backend\artisan migrate
php -c .\php.ini backend\artisan serve
```

Se o `artisan serve` não conseguir subir (falha ao escutar na porta), use o servidor embutido do PHP:

```powershell
php -c .\php.ini -S 127.0.0.1:8000 -t backend\public backend\server.php
```

### Deploy com Docker no Render

O repositório já inclui os arquivos necessários para deploy Docker no Render:

- [Dockerfile](file:///h:/Users/Gui/Desktop/Croche/Dockerfile)
- [.dockerignore](file:///h:/Users/Gui/Desktop/Croche/.dockerignore)
- [render.yaml](file:///h:/Users/Gui/Desktop/Croche/render.yaml)
- [start-container.sh](file:///h:/Users/Gui/Desktop/Croche/docker/start-container.sh)

Guia completo:

- [deploy-render.md](file:///h:/Users/Gui/Desktop/Croche/docs/deploy-render.md)
