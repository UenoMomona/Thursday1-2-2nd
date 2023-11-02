FROM php:8.1-fpm-alpine AS php
RUN apk add -U --no-cache curl-dev
RUN docker-php-ext-install curl

RUN docker-php-ext-install exif

RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS
RUN pecl install apcu
RUN docker-php-ext-enable apcu
RUN pecl clear-cache

# RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev 
RUN apk add libpng-dev
RUN docker-php-ext-install gd

RUN docker-php-ext-install pdo_mysql

RUN install -o www-data -g www-data -d /var/www/upload/image/

COPY ./php.ini ${PHP_INI_DIR}/php.ini

RUN apk add git
RUN git clone https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis
RUN docker-php-ext-install redis
