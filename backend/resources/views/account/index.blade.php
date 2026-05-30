<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Minha conta - Philos Crochê</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Aguafina+Script&family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/account.css') }}" />
  </head>
  <body>
    @include('partials.topbar')

    <main class="container accountPage">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <span>›</span>
        <span>Minha conta</span>
      </nav>

      @if (session('account_error'))
        <div class="alert alert--error">{{ session('account_error') }}</div>
      @endif

      @if (session('status'))
        <div class="alert alert--success">{{ session('status') }}</div>
      @endif

      @if (session('status_password'))
        <div class="alert alert--success">{{ session('status_password') }}</div>
      @endif

      @if (session('status_notifications'))
        <div class="alert alert--success">{{ session('status_notifications') }}</div>
      @endif

      <div class="accountLayout">
        <aside class="accountSidebar" aria-label="Navegação da conta">
          <div class="accountSidebar__intro">
            <div class="accountSidebar__bar"></div>
            <div>
              <h1>Olá {{ explode(' ', trim($user->NOME ?? 'Cliente'))[0] ?? 'Cliente' }}</h1>
              <p>Gerencie seu perfil, senha e preferências em um só lugar.</p>
            </div>
          </div>

          <nav class="accountMenu">
            <a class="accountMenu__item" href="#">
              <span>◫</span>
              <span>Meus pedidos</span>
            </a>
            <a class="accountMenu__item" href="{{ route('favorites.index') }}">
              <span>♡</span>
              <span>Favoritos</span>
            </a>
            <a class="accountMenu__item is-active" href="{{ route('account.index') }}">
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
              <span>Sair / Encerrar</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
          </nav>

          <div class="supportCard">
            <div class="supportCard__title">Precisa de ajuda?</div>
            <p>Se algo não funcionar como esperado, fale com o suporte técnico.</p>
            <a href="mailto:suporte@philoscroche.com.br">suporte@philoscroche.com.br</a>
          </div>
        </aside>

        <section class="accountContent" aria-label="Dados da conta">
          <div class="sectionTitle">
            <span class="sectionTitle__bar"></span>
            <h2>Minha conta</h2>
          </div>

          <div class="accountGrid">
            <section class="card profileCard">
              <div class="card__header">
                <h3>Perfil do usuário</h3>
                <p>Atualize seus dados pessoais com segurança.</p>
              </div>

              <div class="profileHero">
                <div class="profileAvatar">
                  @if ($user->IMG_URL)
                    <img src="{{ $user->IMG_URL }}" alt="{{ $user->NOME }}" />
                  @else
                    <span>{{ strtoupper(substr($user->NOME ?? 'U', 0, 1)) }}</span>
                  @endif
                </div>

                <div class="profileMeta">
                  <div class="profileMeta__name">{{ $user->NOME ?? 'Usuário' }}</div>
                  <div class="profileMeta__email">{{ $user->EMAIL ?? 'email@exemplo.com' }}</div>
                  <div class="profileMeta__date">
                    Cadastro:
                    <strong>
                      {{ $registeredAt ? \Illuminate\Support\Carbon::parse($registeredAt)->format('d/m/Y H:i') : 'não informado no banco atual' }}
                    </strong>
                  </div>
                </div>
              </div>

              <form method="post" action="{{ route('account.profile.update') }}" class="accountForm js-loading-form">
                @csrf
                @method('PATCH')

                <div class="formGrid">
                  <label class="field">
                    <span>Nome completo</span>
                    <input type="text" name="NOME" value="{{ old('NOME', $user->NOME) }}" required />
                    @error('NOME')
                      <small class="field__error">{{ $message }}</small>
                    @enderror
                  </label>

                  <label class="field">
                    <span>E-mail</span>
                    <input type="email" name="EMAIL" value="{{ old('EMAIL', $user->EMAIL) }}" required />
                    @error('EMAIL')
                      <small class="field__error">{{ $message }}</small>
                    @enderror
                  </label>

                  <label class="field field--full">
                    <span>Avatar (URL)</span>
                    <input type="url" name="IMG_URL" value="{{ old('IMG_URL', $user->IMG_URL) }}" placeholder="https://..." />
                    @error('IMG_URL')
                      <small class="field__error">{{ $message }}</small>
                    @enderror
                  </label>
                </div>

                <button class="btnPrimary" type="submit" data-loading-text="Salvando perfil...">Salvar alterações</button>
              </form>
            </section>

            <section class="card passwordCard">
              <div class="card__header">
                <h3>Gerenciar senha</h3>
                <p>Use uma senha forte com pelo menos 8 caracteres, letras, números e caracteres especiais.</p>
              </div>

              <form method="post" action="{{ route('account.password.update') }}" class="accountForm js-loading-form">
                @csrf
                @method('PATCH')

                <div class="formGrid">
                  <label class="field field--full">
                    <span>Senha atual</span>
                    <input type="password" name="current_password" required />
                    @if ($errors->getBag('password')->first('current_password') || $errors->first('current_password'))
                      <small class="field__error">
                        {{ $errors->getBag('password')->first('current_password') ?: $errors->first('current_password') }}
                      </small>
                    @endif
                  </label>

                  <label class="field">
                    <span>Nova senha</span>
                    <input type="password" name="new_password" required />
                    @error('new_password')
                      <small class="field__error">{{ $message }}</small>
                    @enderror
                  </label>

                  <label class="field">
                    <span>Confirmar nova senha</span>
                    <input type="password" name="new_password_confirmation" required />
                  </label>
                </div>

                <button class="btnPrimary" type="submit" data-loading-text="Atualizando senha...">Alterar senha</button>
              </form>
            </section>

            <section class="card activityCard">
              <div class="card__header">
                <h3>Atividades recentes</h3>
                <p>Últimas interações registradas na sua conta.</p>
              </div>

              <div class="timeline">
                @foreach ($activities as $activity)
                  <div class="timeline__item">
                    <div class="timeline__stamp">
                      <strong>{{ \Illuminate\Support\Carbon::parse($activity->created_at)->format('d/m/Y') }}</strong>
                      <span>{{ \Illuminate\Support\Carbon::parse($activity->created_at)->format('H:i') }}</span>
                    </div>
                    <div class="timeline__content">
                      <div class="timeline__title">{{ $activity->action }}</div>
                      <div class="timeline__text">{{ $activity->description }}</div>
                    </div>
                  </div>
                @endforeach
              </div>
            </section>

            <section class="card notificationsCard">
              <div class="card__header">
                <h3>Preferências de notificação</h3>
                <p>Escolha como deseja receber comunicados da plataforma.</p>
              </div>

              <form method="post" action="{{ route('account.notifications.update') }}" class="accountForm js-loading-form">
                @csrf
                @method('PATCH')

                <div class="toggleList">
                  <label class="toggleCard">
                    <div>
                      <strong>E-mail</strong>
                      <span>Receba novidades, alertas e confirmações por e-mail.</span>
                    </div>
                    <input type="checkbox" name="email_enabled" value="1" {{ old('email_enabled', $preferences->email_enabled) ? 'checked' : '' }} />
                  </label>

                  <label class="toggleCard">
                    <div>
                      <strong>Push</strong>
                      <span>Ative notificações push para novidades em tempo real.</span>
                    </div>
                    <input type="checkbox" name="push_enabled" value="1" {{ old('push_enabled', $preferences->push_enabled) ? 'checked' : '' }} />
                  </label>

                  <label class="toggleCard">
                    <div>
                      <strong>SMS</strong>
                      <span>Receba atualizações rápidas sobre pedidos e segurança.</span>
                    </div>
                    <input type="checkbox" name="sms_enabled" value="1" {{ old('sms_enabled', $preferences->sms_enabled) ? 'checked' : '' }} />
                  </label>
                </div>

                <button class="btnPrimary" type="submit" data-loading-text="Salvando preferências...">Salvar preferências</button>
              </form>
            </section>

            <section class="card dangerCard" id="danger-zone">
              <div class="card__header">
                <h3>Zona de perigo</h3>
                <p>Essa ação remove permanentemente a conta e os dados associados criados nesta aplicação.</p>
              </div>

              <form method="post" action="{{ route('account.destroy') }}" class="accountForm js-loading-form js-delete-form">
                @csrf
                @method('DELETE')

                <div class="dangerSteps">
                  <label class="checkboxField">
                    <input type="checkbox" name="delete_confirmation" value="1" {{ old('delete_confirmation') ? 'checked' : '' }} />
                    <span>Confirmo que desejo excluir minha conta permanentemente.</span>
                  </label>
                  @error('delete_confirmation')
                    <small class="field__error">{{ $message }}</small>
                  @enderror

                  <label class="field field--full">
                    <span>Digite <strong>EXCLUIR</strong> para confirmar</span>
                    <input type="text" name="delete_phrase" value="{{ old('delete_phrase') }}" />
                    @error('delete_phrase')
                      <small class="field__error">{{ $message }}</small>
                    @enderror
                  </label>
                </div>

                <div class="dangerActions">
                  <button class="btnDanger" type="submit" data-loading-text="Excluindo conta...">Excluir conta</button>
                  <a class="supportLink" href="mailto:suporte@philoscroche.com.br">Falar com o suporte técnico</a>
                </div>
              </form>
            </section>
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

    <script>
      document.querySelectorAll('.js-loading-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (form.classList.contains('js-delete-form')) {
            var ok = window.confirm('Tem certeza de que deseja excluir sua conta? Esta ação não pode ser desfeita.');

            if (!ok) {
              event.preventDefault();
              return;
            }
          }

          var button = form.querySelector('button[type="submit"]');

          if (!button) {
            return;
          }

          button.dataset.originalText = button.textContent;
          button.textContent = button.dataset.loadingText || 'Carregando...';
          button.disabled = true;
          form.classList.add('is-submitting');
        });
      });
    </script>
  </body>
</html>
