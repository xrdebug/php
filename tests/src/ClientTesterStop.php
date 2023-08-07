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

use Chevere\Xr\Exceptions\StopException;
use Chevere\Xr\Interfaces\ClientInterface;
use Chevere\Xr\Interfaces\MessageInterface;
use Chevere\Xr\Traits\ClientTrait as TraitsClientTrait;
use function Chevere\Message\message;

final class ClientTesterStop implements ClientInterface
{
    use TraitsClientTrait;

    public function sendPause(MessageInterface $message): void
    {
        throw new StopException(
            message('stop')
        );
    }

    public function exit(int $exitCode = 0): void
    {
        echo "exit {$exitCode}";
    }
}
