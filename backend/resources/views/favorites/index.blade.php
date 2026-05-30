<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Favoritos - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400&family=Montserrat:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/favorites.css') }}" />
  </head>
  <body>
    @include('partials.topbar')

    <main class="container favoritesPage">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <span>›</span>
        <a href="#">Minha conta</a>
        <span>›</span>
        <span>Favoritos</span>
      </nav>

      <div class="favoritesLayout">
        <aside class="accountSidebar" aria-label="Navegação da conta">
          <div class="accountSidebar__intro">
            <div class="accountSidebar__bar"></div>
            <div>
              <h1>Olá {{ $customerName }}</h1>
              <p>Bem vindo a sua conta</p>
            </div>
          </div>

          <nav class="accountMenu">
            <a class="accountMenu__item" href="#">
              <span>◫</span>
              <span>Meus pedidos</span>
            </a>
            <a class="accountMenu__item is-active" href="{{ route('favorites.index') }}">
              <span>♡</span>
              <span>Favoritos</span>
            </a>
            <a class="accountMenu__item" href="{{ route('account.index') }}">
              <span>👤</span>
              <span>Minhas informações</span>
            </a>
            @if(auth()->check() && auth()->user()->PERFIL === 'admin')
            <a class="accountMenu__item" href="{{ url('/admin/produtos') }}">
              <span>⚙</span>
              <span>Painel Admin</span>
            </a>
            @endif
            <a class="accountMenu__item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <span>↪</span>
              <span>Sair</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
          </nav>
        </aside>

        <section class="favoritesContent" aria-label="Lista de favoritos">
          <h2 class="favoritesContent__title">Favoritos</h2>

          @forelse ($favorites as $favorite)
            @php
              $product = $favorite['product'];
              $favImageUrl = $product->IMG_URL ?: (!empty($product->IMAGENS) && is_array($product->IMAGENS) ? $product->IMAGENS[0] : null);
            @endphp
            <article class="favoriteRow">
              <form method="post" action="{{ route('favorites.destroy', $product) }}" style="display: contents;">
                @csrf
                @method('DELETE')
                <button class="favoriteRow__remove" type="submit" aria-label="Remover dos favoritos">×</button>
              </form>

              <a class="favoriteRow__thumb" href="{{ route('products.show', $product) }}">
                @if ($favImageUrl)
                  <img src="{{ $favImageUrl }}" alt="{{ $product->CODIGO ?? 'Produto' }}" />
                @else
                  <span class="favoriteRow__thumbPlaceholder">Sem imagem</span>
                @endif
              </a>

              <div class="favoriteRow__info">
                <a class="favoriteRow__name" href="{{ route('products.show', $favorite['product']) }}">
                  {{ $favorite['product']->CODIGO ?? 'Produto' }}
                </a>
                <div class="favoriteRow__color">Cor : <span>{{ ucfirst($favorite['color']) }}</span></div>
              </div>

              <div class="favoriteRow__price">R${{ number_format($favorite['product']->VALOR ?? 0, 2, ',', '.') }}</div>

              <form method="post" action="{{ route('cart.items.store') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="quantity" value="1" />
                <input type="hidden" name="color" value="{{ $favorite['color'] ?? 'natural' }}" />
                <input type="hidden" name="size" value="unico" />
                <button class="favoriteRow__cta" type="submit">Adicionar ao carrinho</button>
              </form>
            </article>
          @empty
            <div class="favoritesEmpty">
              <h2>Você ainda não tem favoritos</h2>
              <p>Salve os produtos que mais gostou para encontrar depois.</p>
              <a class="favoritesEmpty__cta" href="{{ route('products.index') }}">Explorar catálogo</a>
            </div>
          @endforelse
        </section>
      </div>
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
