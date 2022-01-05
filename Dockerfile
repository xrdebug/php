FROM php:8-cli

COPY . .

EXPOSE 27420

ENTRYPOINT [ "php", "server.php", "-p", "27420" ]