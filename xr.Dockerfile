FROM composer:latest as composer
FROM php:8-cli
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get install -y \
    && php -m

COPY . .

RUN composer install \
    --prefer-dist \
    --no-progress \
    --classmap-authoritative \
    --ignore-platform-reqs

EXPOSE 27420

ENTRYPOINT [ "php", "server.php", "27420" ]