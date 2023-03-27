FROM php:8-fpm

RUN apt-get update \
    && apt-get install -y zlib1g-dev libpng-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd