FROM php:8-cli

ENV XR_SERVER_HOST=host.docker.internal \
    XR_SERVER_PORT=27420

COPY . .

EXPOSE 27420

ENTRYPOINT [ "php", "server.php", "27420" ]