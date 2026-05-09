FROM php:8.2-apache

# Sistem kütüphaneleri
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

RUN a2enmod rewrite

# Composer yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Render'da bellek sorununu önlemek için composer ayarı
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# İzinler
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Apache port ayarı
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Render'ın portunu dinle
ENV PORT 80
EXPOSE 80

CMD ["apache2-foreground"]
