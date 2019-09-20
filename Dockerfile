ARG PHP_VERSION=7.4-rc
ARG ALPINE_VERSION=3.10
ARG NGINX_VERSION=1.17

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS php

RUN set -eux; \
    apk add --no-cache postgresql-client libpq make git acl unzip oniguruma; \
    apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} postgresql-dev oniguruma-dev; \
    docker-php-ext-install pdo_pgsql pcntl mbstring opcache; \
    apk del .build-deps


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /app

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]




FROM nginx:${NGINX_VERSION}-alpine AS nginx

RUN rm /etc/nginx/conf.d/default.conf;
COPY docker/nginx/default.nginx.conf /etc/nginx/conf.d/

WORKDIR /app


EXPOSE 80
