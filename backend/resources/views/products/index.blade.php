<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $title ?? 'Produtos' }} - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400&family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/products.css') }}" />
  </head>
  <body>
    @include('partials.topbar')

    <main class="container catalog">
      <div class="catalog__grid">
        <aside class="filters" aria-label="Filtros">
          <div class="filters__card">
            <div class="filters__header">
              <div class="filters__title">Filtro</div>
              <div class="filters__icon" aria-hidden="true">⟂</div>
            </div>

            <div class="filters__section">
              <div class="filters__sectionTitle">Categorias</div>
              @foreach($categories as $cat)
                <a class="filters__row" href="{{ route('products.index', ['category' => $cat->NOME]) }}"><span>{{ $cat->NOME }}</span><span class="filters__chev">›</span></a>
              @endforeach
            </div>
          </div>
        </aside>

        <section class="results" aria-label="Lista de produtos">
          <h1 class="results__title">{{ $title ?? 'Produtos' }}</h1>
          <div class="products">
            @foreach ($products as $product)
              @php
                $imageUrl = $product->IMG_URL ?: (!empty($product->IMAGENS) && is_array($product->IMAGENS) ? $product->IMAGENS[0] : null);
              @endphp
              <article class="pCard">
                <a href="{{ route('products.show', $product) }}" style="display: block; text-decoration: none;">
                  @if($imageUrl)
                    <div class="pCard__img" style="background: url('{{ $imageUrl }}') center/cover"></div>
                  @else
                    <div class="pCard__img" style="background: linear-gradient(135deg, #f5f5fa, #ececf6)"></div>
                  @endif
                </a>
                <form class="pCard__wish" method="post" action="{{ route('favorites.toggle') }}">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $product->id }}" />
                  <button type="submit" aria-label="Favoritar" style="all: unset; width: 100%; height: 100%; display: grid; place-items: center; cursor: pointer;">♡</button>
                </form>
                <a href="{{ route('products.show', $product) }}" style="display: block; text-decoration: none;">
                  <div class="pCard__meta">
                    <div class="pCard__text">
                      <div class="pCard__name">{{ $product->CODIGO ?? $product->NOME ?? 'Produto' }}</div>
                      <div class="pCard__cat">{{ $product->category->NOME ?? 'Sem categoria' }}</div>
                    </div>
                    <div class="pCard__price">R$ {{ number_format($product->VALOR, 2, ',', '.') }}</div>
                  </div>
                </a>
              </article>
            @endforeach
          </div>
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
