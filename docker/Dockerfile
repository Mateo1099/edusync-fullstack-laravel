# syntax=docker/dockerfile:1

# Etapa 1: dependencias PHP con Composer
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# Copiamos el resto del proyecto para optimizar autoload
COPY . .
RUN composer dump-autoload --optimize

# Etapa 2: runtime PHP-FPM
FROM php:8.2-fpm-alpine AS app
WORKDIR /var/www/html

# Extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar código y vendor desde la etapa vendor
COPY --from=vendor /app /var/www/html

# Permisos sobre storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto FPM (interno)
EXPOSE 9000
CMD ["php-fpm"]
