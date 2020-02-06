FROM composer:1.9.3 AS composer
FROM php:7.3-cli
RUN apt-get update && apt-get install -y libpq-dev libzip-dev zip
RUN docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip pdo pdo_pgsql
ADD . /app
WORKDIR /app
COPY --from=composer /usr/bin/composer /usr/bin/composer
CMD ["php", "application.php"]