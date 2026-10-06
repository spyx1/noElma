FROM php:8.4-fpm-alpine

RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" bcmath intl mbstring opcache pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

