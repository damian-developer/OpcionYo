# Usar una imagen base oficial de PHP con soporte para Composer
FROM php:8.3-cli

# Instalar dependencias necesarias para Composer y Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install zip gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

RUN git config --global --add safe.directory /var/www/html

# Cambiar el propietario de los archivos al usuario actualo al contenedor
RUN chown -R www-data:www-data /var/www/html

# Copiar todos los archivos del proyecto al contenedor
COPY . .

# Instalar las dependencias de Composer
RUN composer install

# Copiar el archivo .env.example a .env
RUN cp .env.example .env

# Generar la key de la aplicación
RUN php artisan key:generate

# Ejecutar las migraciones con los seeders
RUN php artisan migrate

RUN php artisan db:seed

# Exponer el puerto 8000 para el servidor de desarrollo de Laravel
EXPOSE 8000

# Comando por defecto para iniciar el servidor de desarrollo
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]