FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo_mysql

RUN apk add --no-cache libmemcached-dev zlib-dev ${PHPIZE_DEPS} && \
    apk add rabbitmq-c-dev && \
    pecl install memcached && \
    pecl install xdebug && \
    pecl install amqp && \
    docker-php-ext-enable memcached xdebug amqp
