# Usar PHP 8.1 FPM basado en Alpine
FROM php:8.1-fpm-alpine

# Instalar dependencias necesarias, incluidas las extensiones de PHP
RUN apk update && apk add --no-cache \
    nginx \
    bash \
    && docker-php-ext-install pdo pdo_mysql

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente de tu aplicación Laravel
COPY . /var/www/html

# Copiar el archivo de configuración de Nginx
COPY ./nginx/default.conf /etc/nginx/conf.d/

# Establecer los permisos adecuados para las carpetas de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80 para Nginx
EXPOSE 80

# Comando para iniciar PHP-FPM y Nginx
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]