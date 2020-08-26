FROM php:7.4-fpm

ARG GITHUB_TOKEN

RUN apt-get update && apt-get install -y

RUN apt-get install -y \
  git \
  libzip-dev \
  zip \
  unzip \
  libpq-dev \
  vim

RUN docker-php-ext-install \
  pdo \
  zip \
  pdo_pgsql

COPY php.ini /usr/local/etc/php/conf.d

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer config -g github-oauth.github.com ${GITHUB_TOKEN}
