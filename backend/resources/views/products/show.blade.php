<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $product->CODIGO ?? 'Produto' }} - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400&family=Montserrat:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/products.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/product-show.css') }}" />
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
          <label class="search" aria-label="Buscar">
            <span class="search__icon" aria-hidden="true">⌕</span>
            <input class="search__input" type="search" placeholder="O que você procura hoje?" />
          </label>
          <div class="iconbar" aria-label="Ações">
            <a class="iconbtn" href="{{ route('favorites.index') }}" aria-label="Favoritos">♡</a>
            <a class="iconbtn" href="{{ route('account.index') }}" aria-label="Conta">👤</a>
            <a class="iconbtn" href="{{ route('cart.index') }}" aria-label="Carrinho">🛒</a>
          </div>
        </div>
      </div>
      <div class="topbar__divider" role="presentation"></div>
    </header>

    <main class="container productPage">
      <section class="productHero" aria-label="Detalhes do produto">
        <div class="productGallery">
          <div class="productGallery__thumbs">
            @for ($i = 0; $i < 3; $i++)
              <button class="productGallery__thumb{{ $i === 1 ? ' is-active' : '' }}" type="button" aria-label="Miniatura do produto {{ $i + 1 }}">
                @if ($product->IMG_URL)
                  <img src="{{ $product->IMG_URL }}" alt="{{ $product->CODIGO ?? 'Produto' }}" />
                @else
                  <span class="productGallery__placeholder"></span>
                @endif
              </button>
            @endfor
            <span class="productGallery__dot" aria-hidden="true"></span>
          </div>

          <div class="productGallery__main">
            @if ($product->IMG_URL)
              <img src="{{ $product->IMG_URL }}" alt="{{ $product->CODIGO ?? 'Produto' }}" />
            @else
              <div class="productGallery__fallback">Sem imagem</div>
            @endif
          </div>
        </div>

        <div class="productInfo">
          <div class="productInfo__head">
            <h1 class="productInfo__title">{{ $product->CODIGO ?? 'Produto sem nome' }}</h1>
            <div class="productInfo__rating">
              <span class="productInfo__stars">★★★★★</span>
              <span class="productInfo__score">4.5</span>
              <span class="productInfo__reviews">10 comentários</span>
            </div>
          </div>

          <div class="productInfo__colors">
            <div class="productInfo__label">Cores disponíveis</div>
            <div class="productInfo__swatches">
              <span class="productInfo__swatch" style="background:#26385b"></span>
              <span class="productInfo__swatch" style="background:#f0cc3f"></span>
              <span class="productInfo__swatch" style="background:#f3a1c2"></span>
              <span class="productInfo__swatch" style="background:#9d2348"></span>
            </div>
          </div>

          <div class="productInfo__purchase">
            <a class="productInfo__cta" href="{{ route('cart.index') }}">Adicionar ao carrinho</a>
            <div class="productInfo__price">R$ {{ number_format($product->VALOR ?? 0, 2, ',', '.') }}</div>
          </div>

          <div class="productInfo__features">
            <div class="featureItem">
              <span class="featureItem__icon">◫</span>
              <span>Forma de pagamento</span>
            </div>
            <div class="featureItem">
              <span class="featureItem__icon">◧</span>
              <span>Tamanho</span>
            </div>
            <div class="featureItem">
              <span class="featureItem__icon">◌</span>
              <span>Frete</span>
            </div>
            <div class="featureItem">
              <span class="featureItem__icon">↺</span>
              <span>Troca ou devolução</span>
            </div>
          </div>
        </div>
      </section>

      <section class="productDetails">
        <div class="sectionTitle">
          <span class="sectionTitle__bar"></span>
          <h2>Descrição do produto</h2>
        </div>

        <div class="productDetails__tabs">
          <span class="productDetails__tab is-active">Descrição</span>
          <span class="productDetails__tab">Avaliações</span>
          <span class="productDetails__tab">Dúvidas</span>
        </div>

        <div class="productDetails__content">
          <div class="productDetails__text">
            <p>{{ $product->DESCRICAO ?: 'Produto artesanal em crochê feito com acabamento cuidadoso e ótimo para presentear ou decorar.' }}</p>
            <p>{{ $product->DETALHES ?: 'Produzido manualmente com fios selecionados, textura macia e visual delicado para o dia a dia.' }}</p>

            <div class="productSpecs">
              <div class="productSpecs__item"><strong>Categoria:</strong> {{ $product->category->NOME ?? 'Sem categoria' }}</div>
              <div class="productSpecs__item"><strong>Marca:</strong> {{ $product->marca->NOME ?? 'Philos Crochê' }}</div>
              <div class="productSpecs__item"><strong>Código:</strong> {{ $product->CODIGO ?? '-' }}</div>
              <div class="productSpecs__item"><strong>Destaque:</strong> {{ ($product->DESTAQUE ?? false) ? 'Sim' : 'Não' }}</div>
            </div>
          </div>

          <div class="productDetails__media">
            <div class="videoCard">
              @if ($product->IMG_URL)
                <img src="{{ $product->IMG_URL }}" alt="{{ $product->CODIGO ?? 'Produto' }}" />
              @endif
              <div class="videoCard__overlay">
                <button class="videoCard__play" type="button" aria-label="Reproduzir video">▶</button>
                <div class="videoCard__caption">{{ $product->CODIGO ?? 'Produto' }}</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="similarSection">
        <div class="sectionTitle">
          <span class="sectionTitle__bar"></span>
          <h2>Produtos similares</h2>
        </div>

        <div class="products similarGrid">
          @foreach ($similarProducts as $similarProduct)
            <a class="pCard" href="{{ route('products.show', $similarProduct) }}">
              @if($similarProduct->IMG_URL)
                <div class="pCard__img" style="background: url('{{ $similarProduct->IMG_URL }}') center/cover"></div>
              @else
                <div class="pCard__img" style="background: linear-gradient(135deg, #f5f5fa, #ececf6)"></div>
              @endif
              <a class="pCard__wish" href="{{ route('favorites.index') }}" aria-label="Favoritar">♡</a>
              <div class="pCard__meta">
                <div class="pCard__text">
                  <div class="pCard__name">{{ $similarProduct->CODIGO ?? 'Produto' }}</div>
                  <div class="pCard__cat">{{ $similarProduct->category->NOME ?? 'Sem categoria' }}</div>
                </div>
                <div class="pCard__price">R$ {{ number_format($similarProduct->VALOR ?? 0, 2, ',', '.') }}</div>
              </div>
            </a>
          @endforeach
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
