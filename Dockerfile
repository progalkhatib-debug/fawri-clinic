FROM php:8.3-apache

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip \
    && docker-php-ext-install pdo_mysql gd zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# إعداد قاعدة البيانات والصلاحيات
RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# تشغيل المايجريشن أثناء البناء (بصيغة لا تسبب تعارض)
RUN php artisan migrate --force || true

# تحديد أمر التشغيل الافتراضي
CMD ["apache2-foreground"]