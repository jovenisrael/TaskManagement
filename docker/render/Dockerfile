FROM node:22-bookworm AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends nginx git unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

COPY docker/render/nginx.conf /etc/nginx/templates/default.conf
COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/php/zz-clear-env.conf /usr/local/etc/php-fpm.d/zz-clear-env.conf

RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

ENV DB_CONNECTION=mysql \
    DB_PORT=3306 \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr \
    APP_ENV=production \
    APP_DEBUG=false

EXPOSE 10000

ENTRYPOINT ["entrypoint.sh"]
