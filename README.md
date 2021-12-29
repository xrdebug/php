# XR

> 🔔 Subscribe to the [newsletter](https://newsletter.chevereto.com/subscription?f=gTmksA6763vPCG763763kYCOTgWu6Kx4BPohVDY97aHddrqis6B763cHay8dhtmMKlI6r3vUfGREZmSvDNNGj3MlrRJV7A) to don't miss any update regarding Chevere.

![Chevere](LOGO.svg)

![Code size](https://img.shields.io/github/languages/code-size/chevere/chevere?style=flat-square) [![AGPL-3.0-only](https://img.shields.io/github/license/chevere/chevere?style=flat-square)](LICENSE) [![Build](https://img.shields.io/github/workflow/status/chevere/chevere/CI/master?style=flat-square)](https://github.com/chevere/chevere/actions)

A remote dump debugging utility.

## Status

This project is under preview status.

## Getting started

* Clone this repository
* Install the dependencies using Composer

```sh
composer install
```

## Start the dump server

If you added this package as a dependency:

```sh
php vendor/chevere/xr/server.php 9666
```

If you cloned this repository:

```sh
php server.php 9666
```

The server will be available at [http://localhost:9666](http://localhost:9666)

## Sending messages

* POST parameters
  * `body` - The message raw body (HTML)
  * `filePath` - The file path where the message was emitted as `file.php:15`

```sh
POST http://localhost:9666/message
```

## Helper functions

`🚧 Work in progress`

* Add `chevere/xr` as a dev dependency in your project:

```sh
composer require --dev chevere/xr
```

Use `xr($var)` to send any message from your code.
