FROM php:8.1-fpm

WORKDIR /var/www

COPY composer.lock composer.json ./
RUN composer install --no-autoloader --no-dev

COPY . .

RUN docker-php-ext-install pdo pdo_mysql

RUN composer dump-autoload

RUN chown -R www-data:www-data /var/www

EXPOSE 3000

CMD ["php-fpm"]
