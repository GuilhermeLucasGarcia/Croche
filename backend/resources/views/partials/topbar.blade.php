<header class="topbar">
  <div class="container topbar__inner">
    <a class="brand" href="{{ url('/') }}">
      <span class="brand__name">Philos Croche</span>
      @if(!empty($siteTagline))
        <span class="brand__tagline">{{ $siteTagline }}</span>
      @endif
    </a>
    <nav class="nav" aria-label="Categorias">
      @foreach(($topCategories ?? collect())->take(5) as $category)
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
        <a class="iconbtn" href="{{ route('favorites.index') }}" aria-label="Favoritos">♡@if(!empty($favoriteCount))<span class="badge">{{ $favoriteCount }}</span>@endif</a>
        <a class="iconbtn" href="{{ route('account.index') }}" aria-label="Conta">👤</a>
        <a class="iconbtn" href="{{ route('cart.index') }}" aria-label="Carrinho">🛒@if(!empty($cartCount))<span class="badge">{{ $cartCount }}</span>@endif</a>
      </div>
    </div>
  </div>
  <div class="topbar__divider" role="presentation"></div>
</header>
