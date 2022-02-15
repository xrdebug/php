<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\ThrowableHandler\ThrowableHandler;
use function Chevere\Writer\streamFor;
use Chevere\Writer\StreamWriter;
use Chevere\Writer\Writers;
use Chevere\Writer\WritersInstance;
use function Chevere\Xr\registerThrowableHandler;

foreach (['/../', '/../../../../'] as $path) {
    $autoload = __DIR__ . $path . 'vendor/autoload.php';
    if (stream_resolve_include_path($autoload)) {
        require $autoload;

        break;
    }
}

new WritersInstance(
    (new Writers())
        ->withOutput(
            new StreamWriter(
                streamFor('php://stdout', 'w')
            )
        )
        ->withError(
            new StreamWriter(
                streamFor('php://stderr', 'w')
            )
        )
);
set_error_handler(
    ThrowableHandler::ERRORS_AS_EXCEPTIONS
);
register_shutdown_function(
    ThrowableHandler::FATAL_ERROR_HANDLER
);
set_exception_handler(
    ThrowableHandler::CONSOLE_HANDLER
);
registerThrowableHandler(true);

throw new RuntimeException(
    message: message("Ch bah puta la güeá"),
    code: 12345,
    previous: new Exception(
        message: "A la chuchesumare",
        code: 678,
        previous: new LogicException(
            message: "Ese conchesumare",
            code: 0,
        )
    )
);
