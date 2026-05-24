<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $mode === 'edit' ? 'Editar' : 'Novo' }} {{ $strategy->singularLabel() }} - Admin - Philos Crochê</title>
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
          <h1 class="admintitle">{{ $mode === 'edit' ? 'Editar' : 'Novo' }} {{ $strategy->singularLabel() }}</h1>
          <div class="adminsubtitle">{{ $strategy->pluralLabel() }}</div>
        </div>
        <div class="adminheader__actions">
          <a class="btn btn--ghost" data-confirm-leave="1" href="{{ route('admin.index', ['entity' => $strategy->key()]) }}">Voltar</a>
          @if($mode === 'edit')
            <a class="btn btn--ghost" data-confirm-leave="1" href="{{ route('admin.create', ['entity' => $strategy->key()]) }}">Novo</a>
          @endif
        </div>
      </div>

      @if(session('status'))
        <div class="alert alert--success">{{ session('status') }}</div>
      @endif

      @if($errors->has('form'))
        <div class="alert alert--error">{{ $errors->first('form') }}</div>
      @endif

      <div class="card">
        <form id="entityForm" class="form" method="post" action="{{ $mode === 'edit' ? route('admin.update', ['entity' => $strategy->key(), 'id' => $model->getKey()]) : route('admin.store', ['entity' => $strategy->key()]) }}">
          @csrf
          @if($mode === 'edit')
            @method('PUT')
            <input type="hidden" name="id" value="{{ $model->getKey() }}" />
          @endif

          <div class="formgrid">
            @foreach($fields as $field)
              @php
                $name = $field['name'];
                $type = $field['type'] ?? 'text';
                $label = $field['label'] ?? $name;
                $required = (bool) ($field['required'] ?? false);
                $value = old($name, $model ? ($model->{$name} ?? '') : '');
                $hasError = $errors->has($name);
                $errorText = $errors->first($name);
              @endphp

              <div class="field {{ $hasError ? 'field--error' : '' }}" data-field="{{ $name }}">
                <label class="field__label" for="field_{{ $name }}">{{ $label }}@if($required)<span class="field__req">*</span>@endif</label>

                @if($type === 'textarea')
                  <textarea
                    class="field__control"
                    id="field_{{ $name }}"
                    name="{{ $name }}"
                    rows="4"
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                    {{ $required ? 'required' : '' }}
                  >{{ $value }}</textarea>
                @elseif($type === 'select')
                  <select class="field__control" id="field_{{ $name }}" name="{{ $name }}" {{ $required ? 'required' : '' }}>
                    <option value="">{{ $field['placeholder'] ?? 'Selecione' }}</option>
                    @foreach(($field['options'] ?? []) as $optValue => $optLabel)
                      <option value="{{ $optValue }}" {{ (string) $value === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
                    @endforeach
                  </select>
                @elseif($type === 'checkbox')
                  @php
                    $checked = (bool) old($name, $model ? (bool) ($model->{$name} ?? false) : false);
                  @endphp
                  <label class="check">
                    <input id="field_{{ $name }}" class="check__input" type="checkbox" name="{{ $name }}" value="1" {{ $checked ? 'checked' : '' }} />
                    <span class="check__box" aria-hidden="true"></span>
                    <span class="check__label">{{ $field['text'] ?? 'Sim' }}</span>
                  </label>
                @else
                  <input
                    class="field__control"
                    id="field_{{ $name }}"
                    name="{{ $name }}"
                    type="{{ $type }}"
                    value="{{ $type === 'password' ? '' : $value }}"
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                    @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                    @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                    {{ $required ? 'required' : '' }}
                    autocomplete="off"
                    @if($type === 'url') oninput="document.getElementById('preview_{{ $name }}').src = this.value" @endif
                  />
                  @if($type === 'url')
                    <div style="margin-top: 8px;">
                      <img id="preview_{{ $name }}" src="{{ $value }}" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #ccc; display: block;" onerror="this.style.display='none'" onload="this.style.display='block'" />
                    </div>
                  @endif
                @endif

                <div class="field__hint" data-error-for="{{ $name }}">{{ $errorText }}</div>
              </div>
            @endforeach
          </div>

          <div class="formactions">
            <button id="submitBtn" class="btn" type="submit">{{ $mode === 'edit' ? 'Salvar alterações' : 'Cadastrar' }}</button>
            @if($mode === 'edit')
              <button type="button" class="btn btn--ghost" style="color: #d32f2f;" onclick="if(confirm('Tem certeza que deseja excluir este item?')) { document.getElementById('deleteForm').submit(); }">Excluir</button>
            @endif
          </div>
        </form>
        
        @if($mode === 'edit')
        <form id="deleteForm" method="post" action="{{ route('admin.destroy', ['entity' => $strategy->key(), 'id' => $model->getKey()]) }}" style="display: none;">
          @csrf
          @method('DELETE')
        </form>
        @endif
      </div>
    </main>

    <script>
      (function () {
        const form = document.getElementById('entityForm');
        const submitBtn = document.getElementById('submitBtn');
        const csrf = form.querySelector('input[name="_token"]').value;
        const validateUrl = @json(route('admin.validate', ['entity' => $strategy->key()]));
        const entityId = form.querySelector('input[name="id"]') ? form.querySelector('input[name="id"]').value : '';
        let dirty = false;
        let submitting = false;

        const setFieldError = (name, message) => {
          const field = document.querySelector('[data-field="' + name + '"]');
          const hint = document.querySelector('[data-error-for="' + name + '"]');
          if (!field || !hint) return;
          if (message) {
            field.classList.add('field--error');
            hint.textContent = message;
          } else {
            field.classList.remove('field--error');
            hint.textContent = '';
          }
        };

        const validateField = async (name, value) => {
          const body = new FormData();
          body.append('_token', csrf);
          body.append('_validate_field', name);
          if (entityId) body.append('id', entityId);

          if (typeof value === 'boolean') {
            body.append(name, value ? '1' : '0');
          } else if (value === null || typeof value === 'undefined') {
            body.append(name, '');
          } else {
            body.append(name, value);
          }

          try {
            const res = await fetch(validateUrl, { method: 'POST', body, headers: { 'Accept': 'application/json' } });
            if (res.ok) {
              setFieldError(name, '');
              return;
            }
            const data = await res.json();
            const msg = data && data.errors && data.errors[name] ? data.errors[name][0] : '';
            setFieldError(name, msg);
          } catch (e) {
          }
        };

        const debounceMap = new Map();
        const debounce = (key, fn, ms) => {
          if (debounceMap.has(key)) clearTimeout(debounceMap.get(key));
          const t = setTimeout(fn, ms);
          debounceMap.set(key, t);
        };

        form.addEventListener('input', function (e) {
          if (!e.target || !e.target.name) return;
          dirty = true;
          debounce(e.target.name, () => validateField(e.target.name, e.target.type === 'checkbox' ? e.target.checked : e.target.value), 350);
        });

        form.addEventListener('change', function (e) {
          if (!e.target || !e.target.name) return;
          dirty = true;
          debounce(e.target.name, () => validateField(e.target.name, e.target.type === 'checkbox' ? e.target.checked : e.target.value), 50);
        });

        form.addEventListener('submit', function (e) {
          if (submitting) {
            e.preventDefault();
            return;
          }
          submitting = true;
          dirty = false;
          submitBtn.textContent = 'Salvando...';
          setTimeout(() => {
            submitBtn.disabled = true;
          }, 10);
        });

        window.addEventListener('beforeunload', function (e) {
          if (dirty && !submitting) {
            e.preventDefault();
            e.returnValue = '';
            return '';
          }
        });

        document.querySelectorAll('[data-confirm-leave="1"]').forEach((a) => {
          a.addEventListener('click', function (e) {
            if (!dirty || submitting) return;
            const ok = window.confirm('Existem alterações não salvas. Deseja sair mesmo assim?');
            if (!ok) e.preventDefault();
          });
        });
      })();
    </script>
  </body>
</html>
