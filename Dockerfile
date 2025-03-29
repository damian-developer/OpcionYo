FROM php:8.3-fpm-alpine

WORKDIR /var/www/

RUN apk add --no-cache \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    freetype-dev \
    oniguruma-dev \
    mysql-client \
    bash \
    supervisor \
    icu-dev \
    gnu-libiconv \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd
        

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000