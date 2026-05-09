# Resmi ve güvenilir PHP imajı
FROM php:8.2-apache

# Sistem paketlerini ve PHP eklentilerini yükle
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Apache mod_rewrite aktif et (Laravel rotaları için şart)
RUN a2enmod rewrite

# Composer'ı resmi imajdan kopyala
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Proje dosyalarını kopyala
COPY . /var/www/html

# Çalışma dizini
WORKDIR /var/www/html

# Composer bağımlılıklarını yükle
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Dosya izinlerini ayarla
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache'nin public klasörüne bakmasını sağla
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Laravel ayarları
ENV APP_ENV production
ENV APP_DEBUG false

EXPOSE 80
