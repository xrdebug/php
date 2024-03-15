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

use Chevere\ThrowableHandler\ThrowableHandler;
use Chevere\Writer\StreamWriter;
use Chevere\Writer\Writers;
use Chevere\Writer\WritersInstance;
use function Chevere\Writer\streamFor;
use function Chevere\xrDebug\PHP\registerThrowableHandler;

require 'autoload.php';

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
    ThrowableHandler::ERROR_AS_EXCEPTION
);
register_shutdown_function(
    ThrowableHandler::SHUTDOWN_ERROR_AS_EXCEPTION
);
set_exception_handler(
    ThrowableHandler::CONSOLE
);
registerThrowableHandler(true);

throw new RuntimeException(
    message: 'Ch bah puta la güeá',
    code: 12345,
    previous: new Exception(
        message: 'A la chuchesumare',
        code: 678,
        previous: new LogicException(
            message: 'Ese conchesumare',
            code: 0,
        )
    )
);
