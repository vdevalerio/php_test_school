FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Xdebug for coverage
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN { \
    echo "xdebug.mode=off"; \
    echo "xdebug.show_exception_trace=0"; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && { \
    echo "zend.assertions=1"; \
    echo "assert.exception=1"; \
    echo "memory_limit=-1"; \
    } >> /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize