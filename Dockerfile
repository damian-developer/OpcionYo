FROM php:8.4-fpm-alpine

WORKDIR /var/www/

# Instala dependencias necesarias
# npm instala nodejs como dependencia
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
    npm \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath intl gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Da permisos a las carpetas necesarias
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto de PHP-FPM (no es necesario mapearlo en docker-compose)
EXPOSE 9000