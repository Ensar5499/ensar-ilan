FROM richarvey/php-apache:latest

# Proje dosyalarını kopyala
COPY . /var/www/html

# Gerekli Laravel ayarları
ENV WEBROOT /var/www/html/public
ENV APP_ENV production

# Composer bağımlılıklarını yükle
RUN composer install --no-dev
