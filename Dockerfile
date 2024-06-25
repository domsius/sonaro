FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev sendmail unzip

RUN docker-php-ext-install mysqli

RUN docker-php-ext-enable mysqli && a2enmod rewrite

COPY src/ /var/www/html/
COPY php.ini /usr/local/etc/php/

RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY src/composer.json /var/www/html/composer.json
COPY src/composer.lock /var/www/html/composer.lock
RUN composer install