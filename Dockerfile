FROM php:8.2-cli

# install composer

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# vendor install

COPY composer.json /app/composer.json

WORKDIR /app


RUN composer install

COPY . /app


CMD [ "php", "./index.php" ]