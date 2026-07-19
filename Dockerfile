FROM php:8.2-apache

# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip

# تثبيت امتدادات PHP
RUN docker-php-ext-install pdo_mysql gd zip

# تفعيل Rewrite Module
RUN a2enmod rewrite

# استبدال إعدادات Apache الافتراضية بملفنا المخصص
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# نسخ ملفات المشروع
COPY . /var/www/html

# ضبط الصلاحيات للمجلدات التي يحتاجها Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache