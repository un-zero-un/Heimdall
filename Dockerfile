ARG PHP_VERSION=7.4
ARG ALPINE_VERSION=3.10
ARG NGINX_VERSION=1.17
ARG NODE_VERSION=12

FROM node:${NODE_VERSION}-alpine AS node

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS php

RUN set -eux; \
    apk add --no-cache postgresql-client libpq acl oniguruma libstdc++; \
    apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} postgresql-dev oniguruma-dev; \
    docker-php-ext-install pdo_pgsql pcntl mbstring opcache; \
    apk del .build-deps


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /opt/yarn* /opt/yarn

RUN ln -vs /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN ln -vs /opt/yarn/bin/yarn /usr/local/bin/yarn

ARG APP_ENV=prod

WORKDIR /app

COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest; \
    composer clear-cache; \
    mkdir -p var

COPY package.json yarn.lock ./

RUN yarn --pure-lockfile

COPY bin bin/
COPY config config/
COPY fixtures fixtures/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --prefer-dist --no-dev --no-progress --no-suggest; \
    composer dump-autoload --optimize --no-dev --classmap-authoritative; \
    php bin/console cache:clear; \
    chmod +x bin/console; \
    sync

COPY assets assets/
COPY webpack.config.js ./
COPY tsconfig.json ./

RUN set -eux; \
    mkdir -p public; \
    yarn build

VOLUME /app/var/log
VOLUME /app/var/cache

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]




FROM nginx:${NGINX_VERSION}-alpine AS nginx

RUN rm /etc/nginx/conf.d/default.conf
COPY docker/nginx/default.nginx.conf /etc/nginx/conf.d/

WORKDIR /app
COPY --from=php /app/public public/


RUN set -eux; \
    wget -O mkcert https://github.com/FiloSottile/mkcert/releases/download/v1.4.0/mkcert-v1.4.0-linux-amd64; \
    chmod a+x ./mkcert; \
    mkdir -p /app/docker/nginx;\
    ./mkcert -cert-file /app/docker/nginx/localhost.pem -key-file /app/docker/nginx/localhost-key.pem localhost 127.0.0.1 ::1; \
    rm ./mkcert


EXPOSE 80
