FROM richarvey/php-apache:3.1.0

# Gerekli sistem paketlerini yükle
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip

# Proje dosyalarını kopyala
COPY . /var/www/html

# Composer bağımlılıklarını yükle (Vendor klasörü yoksa oluşturur)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Dosya izinlerini ayarla (Laravel'in yazabilmesi için şart)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel ayarları
ENV WEBROOT /var/www/html/public
ENV APP_ENV production
ENV APP_DEBUG false

# Apache ayarlarını ve portu belirle
EXPOSE 80
