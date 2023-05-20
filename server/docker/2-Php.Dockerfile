FROM php:8.2.0alpha1-fpm-alpine AS php-8.2.0
RUN set -ex && apk update
RUN apk add --update linux-headers && apk --no-cache add postgresql-dev
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug-3.2.0
RUN docker-php-ext-install pdo mysqli pdo_mysql && docker-php-ext-enable mysqli pdo_mysql xdebug
RUN docker-php-ext-install opcache
COPY --from=composer:latest  /usr/bin/composer /usr/bin/composer
RUN mkdir -p "$PHP_INI_DIR/conf.d/"
COPY ./build/php.opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
