#!/bin/sh
set -eu

cd /var/www/html

PORT="${PORT:-10000}"

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY nao definido. Configure a variavel de ambiente APP_KEY no Render antes do deploy." >&2
  exit 1
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

php artisan storage:link >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

if [ "${LARAVEL_OPTIMIZE:-true}" = "true" ]; then
  php artisan config:cache
  php artisan route:cache || true
  php artisan view:cache || true
fi

exec php -d variables_order=EGPCS -S "0.0.0.0:${PORT}" -t public server.php
