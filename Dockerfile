FROM php:7.0-apache
WORKDIR /var/www/html/
COPY slim-server/ .

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip
    
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install
