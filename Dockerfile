FROM php:8.2-apache

# 1. Gerekli kütüphaneleri ve MySQL sürücülerini kur
# Hata riskini azaltmak için paketleri tek tek ve temizleme yaparak kuruyoruz
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Apache mod_rewrite aktif (Laravel rotaları için)
RUN a2enmod rewrite

# 3. Composer'ı resmi imajdan kopyala
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Proje dosyalarını çalışma dizinine kopyala
WORKDIR /var/www/html
COPY . .

# 5. Kütüphaneleri kur (Bellek sorununu önlemek için --no-scripts eklendi)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 6. Apache ayarları (Public klasörünü root yapıyoruz)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Render'ın PORT değişkenine uyum sağla
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 8. Yazma izinleri (Kritik)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Başlatma
ENV PORT 80
EXPOSE 80

CMD ["apache2-foreground"]
