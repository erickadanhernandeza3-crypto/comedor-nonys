# Imagen para publicar en Render. En tu XAMPP no se usa para nada.
FROM php:8.2-apache

# mysqli es lo único que necesita el programa; el resto ya viene en la imagen.
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite headers

COPY docker/php.ini /usr/local/etc/php/conf.d/comedor.ini
COPY . /var/www/html/

RUN rm -rf /var/www/html/_viejo /var/www/html/Dockerfile /var/www/html/docker \
 && chown -R www-data:www-data /var/www/html

# Render entrega el puerto en $PORT; Apache viene escuchando el 80.
CMD sed -i "s/^Listen 80$/Listen ${PORT:-10000}/" /etc/apache2/ports.conf \
 && sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT:-10000}>/" /etc/apache2/sites-available/000-default.conf \
 && apache2-foreground
