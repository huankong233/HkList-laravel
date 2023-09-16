FROM composer AS composer

COPY . /app

RUN composer install --optimize-autoloader --no-interaction --no-progress

FROM huankong233/php-nginx:latest

COPY --chown=nobody nginx.conf /etc/nginx/conf.d/default.conf

COPY --chown=nobody --from=composer /app /var/www/html