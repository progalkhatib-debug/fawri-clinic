FROM php:8.3-apache
# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev git unzip \
    && docker-php-ext-install pdo_mysql gd zip \
    && a2enmod rewrite

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# العمل من المجلد الرئيسي
WORKDIR /var/www/html

# نسخ ملفات التعريف فقط أولاً لتسريع البناء وتثبيت المكتبات
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts
# نسخ باقي ملفات المشروع
COPY . .

# ضبط إعدادات Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# ... (باقي الأسطر السابقة كما هي)

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# إضافة أمر تشغيل المهاجرات عند بدء الحاوية
# ملاحظة: هذا الأمر سيتم تنفيذه عند بناء الحاوية
RUN php artisan migrate --force