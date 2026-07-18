FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip
RUN docker-php-ext-install pdo_mysql gd zip
RUN a2enmod rewrite
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage