FROM php:8.2-apache

# 1. Sistem paketlerini ve MySQL sürücülerini kur (pdo_pgsql yerine pdo_mysql eklendi)
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

# 2. Apache ayarları (Rewrite modu Laravel rotaları için şart)
RUN a2enmod rewrite

# 3. Composer'ı resmi imajdan al
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Proje dosyalarını kopyala
WORKDIR /var/www/html
COPY . .

# 5. Kütüphaneleri kur (Bellek dostu parametreler eklendi)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 6. Apache'nin 'public' klasörüne bakmasını sağla
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Render'ın dinamik portuna uyum sağla (Kritik!)
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 8. Yazma izinlerini ayarla (storage ve cache için)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Başlatma ayarları
ENV PORT 80
EXPOSE 80

CMD ["apache2-foreground"]
