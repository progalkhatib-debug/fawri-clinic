FROM php:8.3-apache

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev libpq-dev git unzip nodejs \
    && docker-php-ext-install pdo_mysql pdo_pgsql gd zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts

# انسخ package.json فقط
COPY package.json ./
RUN npm install

COPY . .
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

RUN php artisan migrate --force || true

CMD php artisan migrate --force && php artisan db:seed --force && apache2-foreground