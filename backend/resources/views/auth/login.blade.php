<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/account.css') }}" />
    <style>
      .authWrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 300px);
        padding: 40px 20px;
      }
      .authCard {
        width: 100%;
        max-width: 480px;
      }
    </style>
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
            <a class="iconbtn" href="{{ route('favorites.index') }}" aria-label="Favoritos">♡</a>
            <a class="iconbtn" href="{{ route('account.index') }}" aria-label="Minha conta">👤</a>
            <a class="iconbtn" href="{{ route('cart.index') }}" aria-label="Carrinho">🛒</a>
          </div>
        </div>
      </div>
      <div class="topbar__divider" role="presentation"></div>
    </header>

    <main class="container authWrapper">
      <section class="card authCard">
        <div class="card__header" style="text-align: center; margin-bottom: 24px;">
          <h3>Bem-vindo de volta</h3>
          <p>Faça login para acessar sua conta.</p>
        </div>

        @if (session('account_error'))
          <div class="alert alert--error">{{ session('account_error') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert alert--error">
            <ul style="margin: 0; padding-left: 20px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="post" action="{{ route('login.post') }}" class="accountForm js-loading-form" style="margin-top: 0;">
          @csrf

          <div class="formGrid" style="grid-template-columns: 1fr; gap: 20px;">
            <label class="field">
              <span>E-mail</span>
              <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="seu@email.com" />
            </label>

            <label class="field">
              <span>Senha</span>
              <input type="password" name="password" required placeholder="Sua senha" />
            </label>
          </div>

          <button class="btnPrimary" type="submit" data-loading-text="Entrando..." style="width: 100%; margin-top: 24px;">Entrar</button>
        </form>
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

    <script>
      document.querySelectorAll('.js-loading-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
          var button = form.querySelector('button[type="submit"]');
          if (!button) return;
          
          button.dataset.originalText = button.textContent;
          button.textContent = button.dataset.loadingText || 'Carregando...';
          button.disabled = true;
          form.classList.add('is-submitting');
        });
      });
    </script>
  </body>
</html>