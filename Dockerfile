FROM php:8.1.6-fpm-alpine

RUN docker-php-ext-install pdo_mysql

RUN apk add --no-cache libmemcached-dev zlib-dev ${PHPIZE_DEPS} && \
    pecl install memcached && \
    pecl install xdebug && \
    docker-php-ext-enable memcached xdebug