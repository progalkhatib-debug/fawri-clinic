FROM php:8.2-apache

# تثبيت الحزم والمتطلبات
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip \
    && docker-php-ext-install pdo_mysql gd zip \
    && a2enmod rewrite

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد ملفات المشروع
WORKDIR /var/www/html
COPY . .

# تثبيت المكتبات فوراً بعد نسخ الملفات
RUN composer install --no-dev --optimize-autoloader

# ضبط إعدادات Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache