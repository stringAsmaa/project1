FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . . # هذه الخطوة تنسخ جميع الملفات، بما في ذلك .env.example

# الخطوة الهامة: نسخ ملف .env (أو إنشاء واحد جديد إذا لم يكن موجودًا)
COPY .env .env # تأكد من وجود ملف .env في مجلد مشروعك

# أو بدلًا من نسخ .env مباشرةً، يمكنك إنشاء واحد جديد داخل الحاوية:
# RUN cp .env.example .env

RUN rm -rf bootstrap/cache/*.php

RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000

CMD php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan key:generate && \
    php artisan migrate:fresh --seed && \
    php-fpm
