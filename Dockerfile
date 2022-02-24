ARG PHP_VERSION=8.1
ARG ALPINE_VERSION=3.15
ARG NGINX_VERSION=1.21
ARG NODE_VERSION=16

FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION} AS node

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS php

ARG APCU_VERSION=5.1.21
ARG EXTERNAL_USER_ID=1000

RUN set -eux; \
    apk add --no-cache fcgi postgresql-client libpq acl oniguruma libstdc++ libxslt libgcrypt gmp libcurl curl icu; \
    apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} postgresql-dev oniguruma-dev libxslt-dev libgcrypt-dev gmp-dev curl-dev icu-dev; \
    pecl install apcu-${APCU_VERSION}; \
    docker-php-ext-enable apcu; \
    docker-php-ext-install pdo_pgsql intl pcntl mbstring opcache xsl gmp curl; \
    apk del .build-deps


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /opt/yarn* /opt/yarn

RUN ln -vs /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN ln -vs /opt/yarn/bin/yarn /usr/local/bin/yarn



COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint


WORKDIR /app

RUN set -eux; \
    sed -i -r s/"(www-data:x:)([[:digit:]]+):([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:${EXTERNAL_USER_ID}:/g /etc/passwd; \
    sed -i -r s/"(www-data:x:)([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:/g /etc/group; \
    chown -R www-data:www-data /app /home/www-data /usr/local/etc/php

USER www-data
ARG APP_ENV=prod
ARG APP_DEBUG=false



COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest; \
    composer clear-cache; \
    mkdir -p var

COPY package.json yarn.lock ./

RUN yarn --pure-lockfile

COPY --chown=www-data:www-data bin bin/
COPY --chown=www-data:www-data config config/
COPY --chown=www-data:www-data public public/
COPY --chown=www-data:www-data src src/
COPY --chown=www-data:www-data templates templates/
COPY --chown=www-data:www-data translations translations/

RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --prefer-dist --no-dev --no-progress --no-suggest; \
    composer dump-autoload --optimize --no-dev --classmap-authoritative; \
    php bin/console cache:clear; \
    chmod +x bin/console; \
    sync

COPY --chown=www-data:www-data assets assets/
COPY --chown=www-data:www-data webpack.config.js ./
COPY --chown=www-data:www-data tsconfig.json ./

RUN set -eux; \
    mkdir -p public; \
    yarn build



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


FROM php as cron

USER root

RUN crontab -l | { cat; \
    echo "*/2 * * * * /app/bin/console heimdall:run-recorded-checks"; \
    echo "0 23 * * */0 /app/bin/console heimdall:clean-runs"; \
} | crontab -u www-data -

HEALTHCHECK --interval=1m --timeout=30s --retries=3 CMD php -v || exit 1

WORKDIR /app

ENTRYPOINT []

CMD ["crond", "-f"]
