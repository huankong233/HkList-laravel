FROM composer AS composer

COPY . /app

RUN composer install --optimize-autoloader --no-interaction --no-progress

FROM huankong233/php-nginx:latest

COPY --chown=nobody nginx.conf /etc/nginx/conf.d/default.conf

# 复制到文件夹内，运行时判断是否复制
COPY --chown=nobody --from=composer /app /var/www/94list-laravel

COPY --chown=nobody entrypoint.sh /var/www/94list-laravel/entrypoint.sh
RUN chmod a+x /var/www/94list-laravel/entrypoint.sh

ENTRYPOINT ["/var/www/94list-laravel/entrypoint.sh"]
# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]