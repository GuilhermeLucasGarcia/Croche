FROM composer:2 AS composer_deps

WORKDIR /app

COPY backend/composer.json backend/composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader


FROM node:22-alpine AS frontend_builder

WORKDIR /app

COPY backend/package.json ./
COPY backend/vite.config.js ./
COPY backend/resources ./resources
COPY backend/public ./public

RUN npm install --no-audit --no-fund
RUN npm run build


FROM php:8.3-cli-alpine AS app

RUN apk add --no-cache \
        curl \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
    && docker-php-ext-install \
        bcmath \
        mbstring \
        pdo_pgsql \
        pgsql \
        zip

WORKDIR /var/www/html

COPY backend/ ./
COPY --from=composer_deps /app/vendor ./vendor
COPY --from=frontend_builder /app/public/build ./public/build
COPY docker/start-container.sh /usr/local/bin/start-container

RUN addgroup -S app && adduser -S app -G app \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R app:app /var/www/html \
    && chmod +x /usr/local/bin/start-container

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    PORT=10000 \
    PHP_CLI_SERVER_WORKERS=4

EXPOSE 10000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
  CMD /bin/sh -c 'curl -fsS "http://127.0.0.1:${PORT:-10000}/up" >/dev/null || exit 1'

USER app

CMD ["start-container"]
