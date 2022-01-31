# Docker

## First run

```sh
docker run -d -p 27420:27420 --name chevere-xr ghcr.io/chevere/xr
```

The server will be available at [http://localhost:27420](http://localhost:27420)

## Demo

Open the debugger and then run:

```php
docker exec -it chevere-xr \
    php demo.php
```

## Start

```sh
docker container start chevere-xr
```

## Stop

```sh
docker container stop chevere-xr
```

## Remove

```sh
docker container rm chevere-xr -f
```

## Build

```sh
docker build -t ghcr.io/chevere/xr:tag .
```

## Docker configuration

When using Docker (local) the host should point to the internal IP of your Docker host by using `host.docker.internal`.

```php
<?php

return [
    // ...
    'host' => 'host.docker.internal',
];
```