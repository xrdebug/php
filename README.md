# xrDebug PHP client

<a href="https://xrdebug.com"><img alt="xrDebug" src="xr.svg" width="40%"></a>

[![Build](https://img.shields.io/github/actions/workflow/status/xrdebug/php/test.yml?branch=3.0&style=flat-square)](https://github.com/xrdebug/php/actions)
![Code size](https://img.shields.io/github/languages/code-size/xrdebug/php?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/xrdebug/php?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fxrdebug%2Fphp%2F3.0)](https://dashboard.stryker-mutator.io/reports/github.com/xrdebug/php/3.0)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=xrdebug_php&metric=alert_status)](https://sonarcloud.io/dashboard?id=xrdebug_php)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=xrdebug_php&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=xrdebug_php)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=xrdebug_php&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=xrdebug_php)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=xrdebug_php&metric=security_rating)](https://sonarcloud.io/dashboard?id=xrdebug_php)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=xrdebug_php&metric=coverage)](https://sonarcloud.io/dashboard?id=xrdebug_php)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=xrdebug_php&metric=sqale_index)](https://sonarcloud.io/dashboard?id=xrdebug_php)
[![CodeFactor](https://www.codefactor.io/repository/github/xrdebug/php/badge)](https://www.codefactor.io/repository/github/xrdebug/php)

## Summary

PHP client library for [xrDebug](https://xrdebug.com/). This library provides a set of functions to dump variables, send raw messages, and interact with the inspector from your codebase.

## Quick start

Install using [Composer](https://packagist.org/packages/xrdebug/php).

```sh
composer require --dev xrdebug/php
```

Use `xr()` directly in your code to dump any variable. For example:

```php
require_once __DIR__ . '/vendor/autoload.php';

// ...
xr('Hello, world!');
```

## Configuring

This xrDebug PHP client uses the following default configuration.

> Skip this section if running from the xrDebug binary and on the same machine.

```php
[
    'isEnabled' => true,
    'isHttps' => false,
    'host' => 'localhost',
    'port' => 27420,
    'key' => '',
]
```

| Property  | Type   | Effect                                    |
| --------- | ------ | ----------------------------------------- |
| isEnabled | bool   | Controls sending messages to the server   |
| isHttps   | bool   | Controls use of https                     |
| host      | string | The host where xrDebug server is running  |
| port      | int    | The Port to connect to the `host`         |
| key       | string | Private key (ed25519) for signed requests |

> `host` The hostname or IP. When running xrDebug on Docker use `host.docker.internal`.

### File-based config

Configure the client by placing a `xr.php` file in project's root directory. Need to define only the properties that override the default config.

We recommend adding `xr.php` to your `.gitignore`.

Here some examples of `xr.php`:

- Run with Docker at a `27980` port:

  ```php
  <?php

  return [
      'host' => 'host.docker.internal',
      'port' => 27980,
  ];
  ```

- Run with sign verification:

  ```php
  <?php

  return [
      'key' => file_get_contents('private.key'),
  ];
  ```

### Code-based config

Use function `xrConfig()` to configure the xrDebug server connection directly in your logic. Need to define only the properties that override the default config.

Here some examples of `xrConfig()`:

- Run with Docker at a `27980` port:

  ```php
   xrConfig(
      host: 'host.docker.internal',
      port: 27980,
  );
  ```

- Run with sign verification:

  ```php
   xrConfig(
      key: file_get_contents('private.key'),
  );
  ```

## Debug helpers

This xrDebug PHP client provides the following helper functions in the root namespace. Use these anywhere in your code.

| Function    | Purpose                      |
| ----------- | ---------------------------- |
| [xr](#xr)   | Dump one or more variables   |
| [xrr](#xrr) | Dump raw message             |
| [xri](#xri) | Dump inspector (pauses, etc) |

### xr

Use function `xr($var1, $var2,...)` to dump one or more variable(s).

```php
xr($var, 'Hola, mundo!');
```

Pass a topic using `t:`.

```php
xr($var, t: 'Epic win');
```

Pass an emote using `e:`.

```php
xr($var, e: '😎');
```

Pass bitwise flags to trigger special behavior.

- `f: XR_BACKTRACE` to include debug backtrace.

```php
xr($var, f: XR_BACKTRACE);
```

### xrr

Use function `xrr()` to send a raw message.

```php
xrr('<h1>Hola, mundo!</h1>');
xrr('<span>Test</span>', t: 'Epic win');
xrr('<b>test</b>', e: '😎');
xrr('some string<br>', f: XR_BACKTRACE);
```

### xri

Use function `xri()` to interact with the inspector.

Use `pause` to pause code execution.

```php
xri()->pause();
```

Use `memory` to send memory usage information.

```php
xri()->memory();
```

### Debug helpers (VarDump)

This xrDebug PHP client also provides the following helper functions provided by the [VarDump](https://chevere.org/packages/var-dump) package.

| Function    | Purpose                           |
| ----------- | --------------------------------- |
| [vd](#vd)   | VarDump to output stream          |
| [vdd](#vdd) | VarDump to output stream  and die |

### vd

Function `vd` is a drop-in replacement for `var_dump`. It prints information about one or more variables to the output stream.

```php
vd($var1, $var2,);
// more code
```

### vdd

Function `vdd` does same as vd, but with die(0) which halts further execution.

```php
vdd($var);
// does exit();
```

## Exception handling

The PHP client provides a throwable handler that can hook or replace existing exception handler logic thanks to the [ThrowableHandler](https://chevere.org/packages/throwable-handler) package.

### Register handler

Use `registerThrowableHandler` to enable xrDebug throwable handling.

```php

use Chevere\xrDebug\PHP\registerThrowableHandler;

// True append xrDebug to your existing handler
// False use only xrDebug handler
registerThrowableHandler(true);
```

## Error handling

To handle errors with xrDebug you will require to configure your project to handle errors as exceptions and register a shutdown function:

```php
use Chevere\ThrowableHandler\ThrowableHandler;

set_error_handler(
    ThrowableHandler::ERROR_AS_EXCEPTION
);
register_shutdown_function(
    ThrowableHandler::SHUTDOWN_ERROR_AS_EXCEPTION
);
```

### Triggered handler

Use `throwableHandler` in any existing exception handler logic:

```php
use Chevere\xrDebug\PHP\throwableHandler;

set_exception_handler(
    function(Throwable $throwable) {
        // ...
        try {
            throwableHandler($throwable);
        } catch(Throwable) {
            // Don't panic
        }
    }
);
```

## Custom inspectors

Extra inspectors can be defined to provide more context aware debug information. To create a custom inspector use `XrInspectorTrait` to implement the `XrInspectorInterface` and use `sendCommand` method.

For code below, `myDump` defines a method that will stream data from your application logic and `myPause` sends a pause with debug backtrace by default.

```php
<?php

use Chevere\xrDebug\PHP\Traits\XrInspectorTrait;
use Chevere\xrDebug\PHP\Interfaces\XrInspectorInterface;

class MyInspector implements XrInspectorInterface
{
    use XrInspectorTrait;

    public function myDump(
        string $t = '',
        string $e = '',
        int $f = 0,
    ): void {
        $data = 'my queries from somewhere...';
        $this->sendCommand(
            command: 'message',
            body: $data,
            topic: $t,
            emote: $e,
            flags: $f,
        );
    }

    public function myPause(
        int $f = XR_DEBUG_BACKTRACE,
    ): void {
        $this->sendCommand(
            command: 'pause',
            flags: $f,
        );
    }
}
```

The method `sendCommand` enables to interact with the existing xrDebug instance.

```php
private function sendCommand(
    string $command,
    string $body = '',
    string $topic = '',
    string $emote = '',
    int $flags = 0
);
```

### Null inspector

A null inspector is required to void any inspection call **if xrDebug is disabled**. The null inspector should implement the same methods as the real inspector, but without carrying any action.

💡 Use `XrInspectorNullTrait` to implement the `XrInspectorInterface` when providing null inspector.

```php
<?php

use Chevere\xrDebug\PHP\Traits\XrInspectorNullTrait;
use Chevere\xrDebug\PHP\Interfaces\XrInspectorInterface;

class MyInspectorNull implements XrInspectorInterface
{
    use XrInspectorNullTrait;

    public function myDump(
        string $t = '',
        string $e = '',
        int $f = 0,
    ): void {
    }

    public function myPause(
        int $f = XR_DEBUG_BACKTRACE,
    ): void {
    }
}
```

### Helper function for custom inspector

```php
use Chevere\xrDebug\PHP\XrInspectorInstance;
use Chevere\xrDebug\PHP\Interfaces\XrInspectorInterface;
use LogicException;
use MyInspector;
use MyInspectorNull;

function my_inspector(): MyInspector
{
    try {
        return XrInspectorInstance::get();
    } catch (LogicException) {
        $inspector = getXr()->enable()
            ? MyInspector::class
            : MyInspectorNull::class;
        $client = getXr()->client();
        $inspector = new $inspector($client);
        $instance = new XrInspectorInstance($inspector);

        return $instance::get();
    }
}
```

To use your custom helper:

```php
my_inspector()->myDump();
my_inspector()->myPause();
```

## Documentation

Documentation available at [docs.xrdebug.com](https://docs.xrdebug.com/).

## License

Copyright [Rodolfo Berrios A.](https://rodolfoberrios.com/)

xrDebug is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
