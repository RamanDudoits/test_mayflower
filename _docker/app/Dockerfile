FROM php:8.3-fpm-alpine

RUN apk add --no-cache --virtual .build-deps autoconf gcc g++ make \
    && apk add --no-cache libstdc++ \
    && pecl install redis \
    && pecl install swoole-5.1.2 \
    && docker-php-ext-install pcntl \
    && docker-php-ext-enable redis swoole \
    && apk del .build-deps

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www
