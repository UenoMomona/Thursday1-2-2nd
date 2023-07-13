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


#RUN echo "file_uploads = On\n" \
#         "upload_max_filesize = 5M\n" \
#         "post_max_size = 5M\n" \
#         > /usr/local/etc/php/conf.d/uploads.ini
