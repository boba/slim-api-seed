FROM composer:latest

FROM phpstan/phpstan:latest

# FROM alpine:latest
FROM php:7.2-alpine

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo 'xdebug.remote_port=9000' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.remote_connect_back=1' >> /usr/local/etc/php/php.ini \
    && apk del pcre-dev ${PHPIZE_DEPS}

ADD . /home/app
WORKDIR /home/app

RUN apk update
RUN apk add --no-cache bash bash-completion bash-doc sed grep coreutils
RUN apk add nodejs nodejs-npm

CMD ["bash", "--login"]
