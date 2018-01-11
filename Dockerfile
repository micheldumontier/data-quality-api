FROM php:7.0-apache

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip && \
    a2enmod rewrite && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html/

COPY slim-server/ .

RUN composer install
