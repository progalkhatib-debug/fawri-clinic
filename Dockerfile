FROM php:8.2-apache

# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تثبيت امتدادات PHP
RUN docker-php-ext-install pdo_mysql gd zip

# تفعيل Rewrite Module
RUN a2enmod rewrite

# استبدال إعدادات Apache الافتراضية
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# نسخ ملفات المشروع
COPY . /var/www/html

# تثبيت مكتبات المشروع باستخدام composer
RUN composer install --no-dev --optimize-autoloader

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache