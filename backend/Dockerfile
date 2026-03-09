FROM php:8.2-fpm

# Install dependensi dan Nginx
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl nginx \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install

# Beri hak akses folder agar Laravel bisa nyimpen file/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy konfigurasi Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# FIX: Tingkatkan batas upload PHP jadi 10MB (Biar form lu nggak gagal/refresh!)
RUN echo "upload_max_filesize = 10M\npost_max_size = 10M" > /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 8080

# Jalankan Nginx dan PHP-FPM secara bersamaan
CMD service nginx start && php-fpm