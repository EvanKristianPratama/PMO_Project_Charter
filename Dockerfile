FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

FROM node:20-alpine AS node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY --from=composer /app/vendor ./vendor
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build
RUN apk add --no-cache bash

FROM php:8.4-fpm-alpine
RUN apk add --no-cache \
    nginx \
    supervisor \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-dev \
    bash \
    curl \
    git \
    nano \
    mysql-client \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring intl zip bcmath \
    && rm -rf /var/cache/apk/*
WORKDIR /var/www/html
COPY . .
COPY --from=composer /app/vendor ./vendor
COPY --from=node /app/public/build ./public/build
RUN rm -f bootstrap/cache/*.php
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && chmod +x /usr/local/bin/docker-entrypoint.sh
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord","-c","/etc/supervisor/conf.d/supervisord.conf"]
