FROM php:8.3-apache

# تثبيت الإضافات الضرورية
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip \
    && docker-php-ext-install pdo_mysql gd zip \
    && a2enmod rewrite

# إعداد Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# تثبيت الحزم (بدون dev)
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts

# نسخ ملفات المشروع
COPY . .
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# إعداد المجلدات والصلاحيات (تم تعميم الصلاحيات لضمان عدم حدوث خطأ Permission denied)
RUN mkdir -p /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs /var/www/html/database && \
    touch /var/www/html/database/database.sqlite && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# تشغيل المايجريشن
RUN php artisan migrate --force || true

# تحديد أمر التشغيل
CMD ["apache2-foreground"]