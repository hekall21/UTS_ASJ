FROM php:8.2-fpm

# FIX: Tambahin libpq-dev biar pdo_pgsql bisa ke-install
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl nginx \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www
COPY . .

RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE 8000

CMD service nginx start && php-fpm