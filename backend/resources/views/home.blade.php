<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400&family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
  </head>
  <body>
    <header class="topbar">
      <div class="container topbar__inner">
        <a class="brand" href="#">
          <span class="brand__name">Philos Croche</span>
        </a>
        <nav class="nav" aria-label="Categorias">
          @foreach($categories->take(5) as $category)
            <a class="nav__link" href="{{ route('products.index', ['category' => $category->NOME]) }}">{{ $category->NOME }}</a>
          @endforeach
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

    <main>
      <section class="hero" aria-label="Banner principal">
        @if($slides->count() > 0)
          @foreach($slides as $index => $slide)
          <style>
            .hero__bg--{{ $slide->id }} {
              background-image: url('{{ $slide->IMG_DESKTOP_URL }}');
            }
            @media (max-width: 768px) {
              .hero__bg--{{ $slide->id }} {
                background-image: url('{{ $slide->IMG_MOBILE_URL }}');
              }
            }
          </style>
          <div class="hero__bg hero__bg--{{ $slide->id }}" style="background-size: cover; background-position: center; {{ $index === 0 ? '' : 'display: none;' }}" data-slide="{{ $index }}">
            <div class="hero__fade"></div>
            <div class="container hero__content">
              <div class="heroCard">
                <h1 class="heroCard__title">{!! nl2br(e($slide->TITULO)) !!}</h1>
                @if($slide->DESCRICAO)
                <p class="heroCard__subtitle">
                  {!! nl2br(e($slide->DESCRICAO)) !!}
                </p>
                @endif
                <a class="btn btn--primary" href="{{ $slide->LINK_DESTINO }}">Comprar agora</a>
              </div>
            </div>
            
            @if($slides->count() > 1)
            <div class="hero__controls" aria-hidden="true">
              <button class="hero__control hero__control--left" type="button" onclick="changeSlide(-1)">‹</button>
              <button class="hero__control hero__control--right" type="button" onclick="changeSlide(1)">›</button>
            </div>
            <div class="hero__dots" aria-hidden="true">
              @foreach($slides as $dotIndex => $s)
              <span class="dot {{ $dotIndex === 0 ? 'dot--active' : '' }}" onclick="goToSlide({{ $dotIndex }})"></span>
              @endforeach
            </div>
            @endif
          </div>
          @endforeach
        @else
        <div class="hero__bg">
          <div class="hero__fade"></div>
          <div class="container hero__content">
            <div class="heroCard">
              <h1 class="heroCard__title">KIT<br />AMIGURUMI</h1>
              <p class="heroCard__subtitle">
                100% artesanal<br />
                Personalizável<br />
                Fio macio e antialérgico
              </p>
              <a class="btn btn--primary" href="{{ route('products.index') }}">Comprar agora</a>
            </div>
          </div>
        </div>
        @endif
      </section>

      <section class="section container" aria-label="Novidades">
        <div class="sectionHeader">
          <span class="sectionHeader__marker" aria-hidden="true"></span>
          <h2 class="sectionHeader__title">Novidades</h2>
        </div>
        <div class="carousel">
          <button class="carousel__arrow" type="button" aria-label="Anterior">‹</button>
          <div class="carousel__track">
            @foreach($categories as $category)
            <a class="catCard" href="{{ route('products.index', ['category' => $category->NOME]) }}">
              <div class="catCard__img" @if($category->IMG_URL) style="background-image: url('{{ $category->IMG_URL }}'); background-size: cover; background-position: center;" @endif aria-hidden="true"></div>
              <div class="catCard__label">{{ $category->NOME }}</div>
            </a>
            @endforeach
          </div>
          <button class="carousel__arrow" type="button" aria-label="Próximo">›</button>
        </div>
      </section>

      <section class="section container" aria-label="Em destaque">
        <div class="sectionHeader">
          <span class="sectionHeader__marker" aria-hidden="true"></span>
          <h2 class="sectionHeader__title">Em destaque</h2>
        </div>
        <div class="productGrid">
          @foreach($featuredProducts as $product)
          @php
            $imageUrl = $product->IMG_URL ?: (!empty($product->IMAGENS) && is_array($product->IMAGENS) ? $product->IMAGENS[0] : null);
          @endphp
          <article class="productCard">
            <button class="wishlist" type="button" aria-label="Favoritar" style="z-index: 2;">♡</button>
            <a href="{{ route('products.show', $product->id) }}" style="display: block; text-decoration: none;">
              <div class="productCard__img" @if($imageUrl) style="background-image: url('{{ $imageUrl }}'); background-size: cover; background-position: center;" @endif aria-hidden="true"></div>
            </a>
            <a href="{{ route('products.show', $product->id) }}" style="display: block; text-decoration: none; color: inherit;">
              <div class="productCard__body">
                <div class="productCard__name">{{ $product->CODIGO }}</div>
                <div class="productCard__meta">
                  <span class="productCard__cat">{{ $product->category->NOME ?? 'Sem categoria' }}</span>
                  <span class="productCard__price">R$ {{ number_format($product->VALOR, 2, ',', '.') }}</span>
                </div>
              </div>
            </a>
          </article>
          @endforeach
        </div>
      </section>

      <section class="section container" aria-label="Feedback">
        <div class="sectionHeader">
          <span class="sectionHeader__marker" aria-hidden="true"></span>
          <h2 class="sectionHeader__title">Feedback</h2>
        </div>
        <div class="feedbackGrid">
          <article class="feedbackCard">
            <div class="feedbackCard__top">
              <div class="avatar avatar--a" aria-hidden="true"></div>
              <div class="stars" aria-label="5 estrelas">★★★★★</div>
            </div>
            <div class="feedbackCard__name">Lorena Yuri</div>
            <p class="feedbackCard__text">
              Perfeito demais! O chaveiro do Homem-Aranha em crochê é super bem feito, cheio de detalhes e muito resistente.
            </p>
          </article>
          <article class="feedbackCard">
            <div class="feedbackCard__top">
              <div class="avatar avatar--b" aria-hidden="true"></div>
              <div class="stars" aria-label="5 estrelas">★★★★★</div>
            </div>
            <div class="feedbackCard__name">Carlos Eduardo</div>
            <p class="feedbackCard__text">
              Comprei a bolsa de crochê para minha esposa e ela amou demais! Muito bem feita, acabamento impecável e super delicada.
            </p>
          </article>
          <article class="feedbackCard">
            <div class="feedbackCard__top">
              <div class="avatar avatar--c" aria-hidden="true"></div>
              <div class="stars" aria-label="5 estrelas">★★★★★</div>
            </div>
            <div class="feedbackCard__name">Henrique Moraes</div>
            <p class="feedbackCard__text">
              Comprei o supla pra minha mãe e ela ficou encantada. Trabalho lindo, cheio de carinho e muito bem feito.
            </p>
          </article>
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
            <a class="social__btn" href="#" aria-label="Instagram">in</a>
            <a class="social__btn" href="#" aria-label="Facebook">f</a>
            <a class="social__btn" href="#" aria-label="YouTube">▶</a>
          </div>
        </div>
      </div>
      <div class="container footer__copy">Copyright © 2026 Philos Croche Ltd. Todos os direitos reservados.</div>
    </footer>
    <script>
      let currentSlide = 0;
      const slides = document.querySelectorAll('.hero__bg[data-slide]');
      const dots = document.querySelectorAll('.hero__dots .dot');

      function showSlide(index) {
        if (slides.length === 0) return;
        
        slides.forEach(slide => slide.style.display = 'none');
        dots.forEach(dot => dot.classList.remove('dot--active'));

        currentSlide = (index + slides.length) % slides.length;
        
        slides[currentSlide].style.display = 'block';
        if (dots[currentSlide]) {
          dots[currentSlide].classList.add('dot--active');
        }
      }

      function changeSlide(direction) {
        showSlide(currentSlide + direction);
      }

      function goToSlide(index) {
        showSlide(index);
      }

      // Auto play
      if (slides.length > 1) {
        setInterval(() => {
          changeSlide(1);
        }, 5000);
      }
    </script>
  </body>
</html>

