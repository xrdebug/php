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

namespace Chevere\Tests\src;

use Chevere\xrDebug\PHP\Exceptions\StopException;
use Chevere\xrDebug\PHP\Interfaces\ClientInterface;
use Chevere\xrDebug\PHP\Interfaces\MessageInterface;
use Chevere\xrDebug\PHP\Traits\ClientTrait as TraitsClientTrait;

final class ClientTesterStop implements ClientInterface
{
    use TraitsClientTrait;

    public function sendPause(MessageInterface $message): void
    {
        throw new StopException('stop');
    }

    public function exit(int $exitCode = 0): void
    {
        echo "exit {$exitCode}";
    }
}
