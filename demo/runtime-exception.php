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
