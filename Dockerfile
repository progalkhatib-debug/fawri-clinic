FROM php:8.2-apache

# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip

# تثبيت امتدادات PHP
RUN docker-php-ext-install pdo_mysql gd zip

# تفعيل Rewrite Module
RUN a2enmod rewrite

# التعديل الأهم: تغيير DocumentRoot الخاص بـ Apache إلى مجلد public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# نسخ ملفات المشروع
COPY . /var/www/html

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage