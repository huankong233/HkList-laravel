FROM composer AS composer

COPY . /app

RUN composer install --optimize-autoloader --no-interaction --no-progress

FROM huankong233/php-nginx:latest

USER root
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/fpm-pool.conf ${PHP_INI_DIR}/php-fpm.d/www.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
COPY --from=composer /app /var/www/94list-laravel

VOLUME ["/var/www/html"]
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]