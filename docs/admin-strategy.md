# Admin CRUD (Strategy)

## Objetivo

Implementar telas reutilizáveis de cadastro/edição para múltiplas entidades sem duplicação de código, isolando regras de validação, persistência e definição de campos em estratégias.

## Componentes

- [AdminFormStrategy.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/Strategies/AdminFormStrategy.php): contrato comum das estratégias.
- [AbstractAdminFormStrategy.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/Strategies/AbstractAdminFormStrategy.php): base com lógica padrão de validação (inclui modo “validação por campo”).
- Estratégias:
  - [ProductStrategy.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/Strategies/ProductStrategy.php)
  - [CategoryStrategy.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/Strategies/CategoryStrategy.php)
  - [UserStrategy.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/Strategies/UserStrategy.php)
- Registry: [AdminStrategyRegistry.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/AdminStrategyRegistry.php)
- Controller genérico: [AdminEntityController.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Http/Controllers/AdminEntityController.php)
- Views compartilhadas:
  - [admin/index.blade.php](file:///h:/Users/Gui/Desktop/Croche/backend/resources/views/admin/index.blade.php)
  - [admin/form.blade.php](file:///h:/Users/Gui/Desktop/Croche/backend/resources/views/admin/form.blade.php)

## Fluxo

1. Rotas `/admin/{entity}` apontam para o controller genérico.
2. O controller resolve a estratégia via registry (por exemplo `produtos` → `ProductStrategy`).
3. A estratégia fornece:
   - consulta base para listagem (`listQuery`)
   - colunas da listagem (`listColumns`)
   - definição de campos do formulário (`fields`)
   - validação (`validateData`)
   - carregamento/criação/atualização (`load/create/update`)

## Validação em tempo real

- A view [admin/form.blade.php](file:///h:/Users/Gui/Desktop/Croche/backend/resources/views/admin/form.blade.php) dispara requisições `POST` para `/admin/{entity}/validar` em `input/change`.
- O request envia `_validate_field` com o nome do campo e somente o valor daquele campo.
- A base [AbstractAdminFormStrategy.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/Strategies/AbstractAdminFormStrategy.php) reduz o conjunto de regras para validar apenas o campo informado.

## Extensão (adicionar nova entidade)

1. Criar uma nova classe em `app/Admin/Strategies/*Strategy.php` implementando `AdminFormStrategy` (ou estendendo `AbstractAdminFormStrategy`).
2. Registrar a estratégia em [AdminStrategyRegistry.php](file:///h:/Users/Gui/Desktop/Croche/backend/app/Admin/AdminStrategyRegistry.php).
3. Incluir a chave da entidade na whitelist das rotas em [web.php](file:///h:/Users/Gui/Desktop/Croche/backend/routes/web.php).
4. Não é necessário criar novas views.

## Campos adicionados ao schema Supabase

As telas admin assumem os seguintes campos (caso não existam, há uma migration com criação condicional):

- `PRODUTO`: `ESTOQUE` (int), `ATIVO` (bool)
- `CATEGORIA`: `CATEGORIA_PAI_ID` (FK lógica)
- `PESSOA`: `ATIVO` (bool)

Migration: [2026_05_24_000001_add_admin_fields_to_supabase_schema.php](file:///h:/Users/Gui/Desktop/Croche/backend/database/migrations/2026_05_24_000001_add_admin_fields_to_supabase_schema.php)

