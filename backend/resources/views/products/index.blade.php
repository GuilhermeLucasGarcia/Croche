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

            <div class="filters__section">
              <div class="filters__sectionTitle">Preço</div>
              <div class="price">
                <div class="price__track" aria-hidden="true"></div>
                <input class="price__range" type="range" min="0" max="200" value="70" />
                <input class="price__range price__range--top" type="range" min="0" max="200" value="200" />
              </div>
              <div class="price__values">
                <div class="price__pill">R$70</div>
                <div class="price__pill">R$200</div>
              </div>
            </div>

            <div class="filters__section">
              <div class="filters__sectionTitle">Cor</div>
              <div class="colors">
                <div class="swatch">
                  <div class="swatch__dot" style="background:#6b2cf5"></div>
                  <div class="swatch__label">Roxo</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#111111"></div>
                  <div class="swatch__label">Preto</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#ff4b2b"></div>
                  <div class="swatch__label">Vermelho</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#1f59ff"></div>
                  <div class="swatch__label">Azul</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#ffffff;border:1px solid rgba(0,0,0,.12)"></div>
                  <div class="swatch__label">Branco</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#c07a2b"></div>
                  <div class="swatch__label">Marrom</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#ffbf3c"></div>
                  <div class="swatch__label">Amarelo</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#cfcfcf"></div>
                  <div class="swatch__label">Cinza</div>
                </div>
                <div class="swatch">
                  <div class="swatch__dot" style="background:#f0a3b6"></div>
                  <div class="swatch__label">Rosa</div>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <section class="results" aria-label="Lista de produtos">
          <h1 class="results__title">{{ $title ?? 'Produtos' }}</h1>
          <div class="products">
            @foreach ($products as $product)
              <a class="pCard" href="{{ route('products.show', $product) }}">
                @if($product->IMG_URL)
                  <div class="pCard__img" style="background: url('{{ $product->IMG_URL }}') center/cover"></div>
                @else
                  <div class="pCard__img" style="background: linear-gradient(135deg, #f5f5fa, #ececf6)"></div>
                @endif
                <a class="pCard__wish" href="{{ route('favorites.index') }}" aria-label="Favoritar">♡</a>
                <div class="pCard__meta">
                  <div class="pCard__text">
                    <div class="pCard__name">{{ $product->CODIGO ?? $product->NOME ?? 'Produto' }}</div>
                    <div class="pCard__cat">{{ $product->category->NOME ?? 'Sem categoria' }}</div>
                  </div>
                  <div class="pCard__price">R$ {{ number_format($product->VALOR, 2, ',', '.') }}</div>
                </div>
              </a>
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
