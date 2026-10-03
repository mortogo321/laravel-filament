# syntax=docker/dockerfile:1.7
# Laravel 13 + Filament 5 — multi-stage production image.
# Pins: php 8.4-cli-bookworm, composer 2.8, bun 1.4.2-alpine.
# Runtime: non-root appuser, HEALTHCHECK on /api/health.

# ── Stage 1: PHP dependencies ────────────────────────────────────────────────
FROM composer:2.9 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# ── Stage 2: frontend assets (Vite 8 + Tailwind 4) ───────────────────────────
FROM oven/bun:1.4.2-alpine AS frontend
WORKDIR /app
COPY package.json bun.lock ./
RUN bun install --frozen-lockfile
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN bun run build

# ── Stage 3: production runtime ──────────────────────────────────────────────
FROM php:8.4-cli-bookworm AS production
ENV DEBIAN_FRONTEND=noninteractive \
    APP_ENV=production

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsqlite3-dev \
    sqlite3 \
    unzip \
  && docker-php-ext-install -j"$(nproc)" \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN useradd -m -u 10001 -s /bin/bash appuser
WORKDIR /app

COPY --from=vendor --chown=appuser:appuser /app/vendor ./vendor
COPY --from=frontend --chown=appuser:appuser /app/public/build ./public/build
COPY --chown=appuser:appuser . .

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache database \
  && touch database/database.sqlite \
  && chown -R appuser:appuser storage bootstrap/cache database \
  && chmod -R 775 storage bootstrap/cache

USER appuser
EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD curl -f http://localhost:8000/api/health || exit 1

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# ── Stage 4: development (sqlite, verbose errors) ────────────────────────────
FROM production AS development
ENV APP_ENV=local \
    APP_DEBUG=true
