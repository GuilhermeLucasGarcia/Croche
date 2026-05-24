<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $strategy->pluralLabel() }} - Admin - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400&family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
  </head>
  <body>
    <header class="topbar">
      <div class="container topbar__inner">
        <a class="brand" href="{{ url('/') }}">
          <span class="brand__name">Philos Croche</span>
        </a>
        <nav class="adminnav" aria-label="Admin">
          @foreach($strategies as $navStrategy)
            <a class="adminnav__link {{ $navStrategy->key() === $strategy->key() ? 'adminnav__link--active' : '' }}" href="{{ route('admin.index', ['entity' => $navStrategy->key()]) }}">{{ $navStrategy->pluralLabel() }}</a>
          @endforeach
        </nav>
        <div class="actions">
          <a class="adminnav__site" href="{{ route('products.index') }}">Ver site</a>
        </div>
      </div>
      <div class="topbar__divider" role="presentation"></div>
    </header>

    <main class="container adminpage">
      <div class="adminheader">
        <div>
          <h1 class="admintitle">{{ $strategy->pluralLabel() }}</h1>
          <div class="adminsubtitle">Cadastro e edição</div>
        </div>
        <div class="adminheader__actions">
          <form class="adminsearch" method="get" action="{{ route('admin.index', ['entity' => $strategy->key()]) }}">
            <input class="adminsearch__input" type="search" name="q" value="{{ request('q') }}" placeholder="Buscar..." />
            <button class="btn btn--ghost" type="submit">Buscar</button>
          </form>
          <a class="btn" href="{{ route('admin.create', ['entity' => $strategy->key()]) }}">Novo</a>
        </div>
      </div>

      @if(session('status'))
        <div class="alert alert--success">{{ session('status') }}</div>
      @endif

      <div class="card">
        <div class="tablewrap">
          <table class="table" aria-label="Lista">
            <thead>
              <tr>
                @foreach($strategy->listColumns() as $col)
                  <th scope="col">{{ $col['label'] }}</th>
                @endforeach
                <th scope="col">Ações</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items as $item)
                <tr>
                  @foreach($strategy->listColumns() as $col)
                    @php
                      $path = explode('.', $col['key']);
                      $value = $item;
                      foreach ($path as $segment) {
                        if (is_null($value)) { break; }
                        $value = $value->{$segment} ?? null;
                      }
                    @endphp
                    <td>
                      @if(is_bool($value))
                        <span class="badge {{ $value ? 'badge--green' : 'badge--gray' }}">{{ $value ? 'Sim' : 'Não' }}</span>
                      @elseif($col['key'] === 'VALOR' && is_numeric($value))
                        R$ {{ number_format((float) $value, 2, ',', '.') }}
                      @else
                        {{ $value ?? '—' }}
                      @endif
                    </td>
                  @endforeach
                  <td class="table__actions">
                    <a class="link" href="{{ route('admin.edit', ['entity' => $strategy->key(), 'id' => $item->getKey()]) }}">Editar</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ count($strategy->listColumns()) + 1 }}" class="table__empty">Nenhum registro encontrado.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($items->hasPages())
          <div class="pagination" aria-label="Paginação">
            <a class="pagination__link {{ $items->onFirstPage() ? 'pagination__link--disabled' : '' }}" href="{{ $items->previousPageUrl() ?? '#' }}">Anterior</a>
            <div class="pagination__meta">Página {{ $items->currentPage() }} de {{ $items->lastPage() }}</div>
            <a class="pagination__link {{ $items->currentPage() === $items->lastPage() ? 'pagination__link--disabled' : '' }}" href="{{ $items->nextPageUrl() ?? '#' }}">Próxima</a>
          </div>
        @endif
      </div>
    </main>
  </body>
</html>

