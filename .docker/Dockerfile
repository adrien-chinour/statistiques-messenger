FROM composer:2 AS composer
FROM php:8.0-cli
RUN apt-get update && apt-get install -y libpq-dev libzip-dev zip jq
RUN docker-php-ext-configure zip && docker-php-ext-install zip pdo pdo_pgsql
ADD . /app
WORKDIR /app
COPY --from=composer /usr/bin/composer /usr/bin/composer
CMD ["php", "application.php"]
