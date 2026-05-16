## Escopo (MVP)

### Loja

- Home (conforme layout enviado)
- Listagem de produtos
- Página de produto (galeria + preço + descrição)
- Carrinho (adicionar/remover/alterar quantidade)
- Checkout (dados do cliente + endereço) criando um pedido
- Histórico do cliente (meus pedidos)

### Admin

- CRUD de categorias
- CRUD de produtos (incluindo imagens)
- Listagem de pedidos + atualização de status (ex.: novo, pago, enviado, concluído, cancelado)

## Modelagem sugerida

### Tabelas principais

- users
- categories
  - id, name, slug, description, is_active
- products
  - id, category_id, name, slug, description, price_cents, stock_qty, is_active
- product_images
  - id, product_id, path, position
- carts
  - id, user_id (nullable), session_id (nullable), totals (opcional)
- cart_items
  - id, cart_id, product_id, quantity, price_cents_snapshot
- orders
  - id, user_id (nullable), customer_name, customer_email, customer_phone
  - shipping_address_* (campos), subtotal_cents, shipping_cents, total_cents
  - status, payment_status
- order_items
  - id, order_id, product_id (nullable), product_name_snapshot, unit_price_cents, quantity

### Regras básicas

- Preços em centavos (inteiro) para evitar problemas de ponto flutuante.
- Slug único por categoria/produto.
- Snapshot de preço/nome no pedido para não “mudar o passado”.
