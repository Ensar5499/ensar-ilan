FROM php:8.2-apache

# 1. Sistem paketlerini ve PostgreSQL sürücülerini kur
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# 2. Composer'ı resmi imajdan çek
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Apache mod_rewrite aktif et
RUN a2enmod rewrite

# 4. Proje dosyalarını kopyala
COPY . /var/www/html
WORKDIR /var/www/html

# 5. Bağımlılıkları kur
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 6. Apache'nin public klasörüne bakmasını sağla
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Render'ın dinamik port ayarı
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 8. İzinleri ayarla
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Başlangıç komutu
# Hata almamak için her şeyi temizleyip sadece sunucuyu başlatıyoruz
ENV PORT 80
EXPOSE 80

CMD php artisan config:clear && php artisan cache:clear && apache2-foreground
