<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Carrinho - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400&family=Montserrat:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}" />
  </head>
  <body>
    <header class="topbar">
      <div class="container topbar__inner">
        <a class="brand" href="{{ url('/') }}">
          <span class="brand__name">Philos Croche</span>
        </a>
        <nav class="nav" aria-label="Categorias">
          <a class="nav__link" href="{{ route('products.index', ['category' => 'Amigurumi']) }}">Amigurumi</a>
          <a class="nav__link" href="{{ route('products.index', ['category' => 'Maternidade']) }}">Maternidade</a>
          <a class="nav__link" href="{{ route('products.index', ['category' => 'Acessórios']) }}">Acessórios</a>
          <a class="nav__link" href="{{ route('products.index', ['category' => 'Decoração']) }}">Decoração</a>
          <a class="nav__link" href="{{ route('products.index', ['category' => 'Outros']) }}">Outros</a>
        </nav>
        <div class="actions">
          <form class="search" action="{{ route('products.index') }}" method="GET" aria-label="Buscar">
            <button type="submit" class="search__icon" aria-label="Buscar" style="background: none; border: none; padding: 0; cursor: pointer;">⌕</button>
            <input class="search__input" type="search" name="q" placeholder="O que você procura hoje?" value="{{ request('q') }}" />
          </form>
          <div class="iconbar" aria-label="Ações">
            @if(auth()->check() && auth()->user()->PERFIL === 'admin')
              <a class="iconbtn" href="{{ url('/admin/produtos') }}" aria-label="Painel Admin" title="Painel Admin" style="color: #6b2cf5;">⚙</a>
            @endif
            <a class="iconbtn" href="{{ route('favorites.index') }}" aria-label="Favoritos">♡</a>
            <a class="iconbtn" href="{{ route('account.index') }}" aria-label="Conta">👤</a>
            <a class="iconbtn" href="{{ route('cart.index') }}" aria-label="Carrinho">🛒</a>
          </div>
        </div>
      </div>
      <div class="topbar__divider" role="presentation"></div>
    </header>

    <main class="container cartPage">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <span>›</span>
        <span>Carrinho</span>
      </nav>

      <section class="cartTable" aria-label="Produtos no carrinho">
        <div class="cartTable__head">
          <div>Detalhes do produto</div>
          <div>Preço</div>
          <div>Quantidade</div>
          <div>Frete</div>
          <div>Subtotal</div>
          <div>Ação</div>
        </div>

        @forelse ($cartItems as $item)
          @php
            $product = $item['product'];
            $cartImageUrl = $product->IMG_URL ?: (!empty($product->IMAGENS) && is_array($product->IMAGENS) ? $product->IMAGENS[0] : null);
          @endphp
          <article class="cartRow">
            <div class="cartRow__product">
              <a class="cartRow__thumb" href="{{ route('products.show', $product) }}">
                @if ($cartImageUrl)
                  <img src="{{ $cartImageUrl }}" alt="{{ $product->CODIGO ?? 'Produto' }}" />
                @else
                  <span class="cartRow__thumbPlaceholder">Sem imagem</span>
                @endif
              </a>

              <div class="cartRow__info">
                <a class="cartRow__name" href="{{ route('products.show', $product) }}">
                  {{ $product->CODIGO ?? 'Produto' }}
                </a>
                <div class="cartRow__meta">Cor: {{ $item['color'] }}</div>
                <div class="cartRow__meta">Tamanho: {{ $item['size'] }}</div>
              </div>
            </div>

            <div class="cartRow__price">R$ {{ number_format($item['price'], 2, ',', '.') }}</div>

            <div class="cartRow__qty">
              <button type="button" aria-label="Diminuir quantidade">−</button>
              <span>{{ $item['quantity'] }}</span>
              <button type="button" aria-label="Aumentar quantidade">+</button>
            </div>

            <div class="cartRow__shipping">{{ $item['shipping'] > 0 ? 'R$ ' . number_format($item['shipping'], 2, ',', '.') : 'Frete gratis' }}</div>
            <div class="cartRow__subtotal">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</div>
            <button class="cartRow__remove" type="button" aria-label="Remover item">🗑</button>
          </article>
        @empty
          <div class="cartEmpty">
            <h2>Seu carrinho está vazio</h2>
            <p>Adicione alguns produtos para continuar a compra.</p>
            <a class="cartEmpty__cta" href="{{ route('products.index') }}">Ir para o catálogo</a>
          </div>
        @endforelse
      </section>

      <section class="cartSummary">
        <div class="cartCoupon">
          <h2 class="cartSummary__title">Cupom de desconto</h2>
          <p class="cartSummary__text">Coloque o cupom de desconto</p>

          <div class="cartCoupon__form">
            <input type="text" placeholder="" aria-label="Cupom de desconto" />
            <button type="button">Aplicar o cupom</button>
          </div>

          <a class="cartCoupon__link" href="{{ route('products.index') }}">Continuar comprando</a>
        </div>

        <div class="cartTotals">
          <div class="cartTotals__row">
            <span>Subtotal</span>
            <strong>R$ {{ number_format($subtotal, 2, ',', '.') }}</strong>
          </div>
          <div class="cartTotals__row">
            <span>Frete</span>
            <strong>R$ {{ number_format($shipping, 2, ',', '.') }}</strong>
          </div>

          <div class="cartTotals__divider"></div>

          <div class="cartTotals__row cartTotals__row--total">
            <span>Total</span>
            <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong>
          </div>

          <button class="cartTotals__cta" type="button">Continuar o pagamento</button>
        </div>
      </section>
    </main>

    <footer class="footer" aria-label="Rodapé">
      <div class="container footer__inner">
        <div class="footerCol">
          <div class="footerCol__title">Sobre a empresa</div>
          <a class="footerCol__link" href="#">Quem somos</a>
          <a class="footerCol__link" href="#">Nossa história</a>
          <a class="footerCol__link" href="#">Propósito e missão</a>
          <a class="footerCol__link" href="#">Produção artesanal</a>
        </div>
        <div class="footerCol">
          <div class="footerCol__title">Mais informações</div>
          <a class="footerCol__link" href="#">Política de privacidade</a>
          <a class="footerCol__link" href="#">Termo de uso</a>
          <a class="footerCol__link" href="#">Trocas e devoluções</a>
          <a class="footerCol__link" href="#">Acompanhe seu pedido</a>
        </div>
        <div class="footerCol">
          <div class="footerCol__title">Atendimento</div>
          <div class="footerCol__text">suporte@philoscroche.com.br</div>
          <div class="footerCol__text">(11) 9xxxx-xxxx</div>
          <div class="social">
            <a class="social__btn" href="#" aria-label="Facebook">f</a>
            <a class="social__btn" href="#" aria-label="Instagram">⌁</a>
            <a class="social__btn" href="#" aria-label="YouTube">▶</a>
            <a class="social__btn" href="#" aria-label="LinkedIn">in</a>
          </div>
        </div>
      </div>
      <div class="container footer__copy">Copyright © 2026 Philos Croche Ltd. Todos os direitos reservados.</div>
    </footer>
  </body>
</html>
