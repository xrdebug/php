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

namespace Chevere\Xr\Tests\_resources;

use function Chevere\Message\message;
use Chevere\Xr\Exceptions\XrStopException;
use Chevere\Xr\Interfaces\XrClientInterface;
use Chevere\Xr\Interfaces\XrMessageInterface;
use Chevere\Xr\Traits\XrClientTrait;

final class XrClientTesterStop implements XrClientInterface
{
    use XrClientTrait;

    public function sendPause(XrMessageInterface $message): void
    {
        throw new XrStopException(
            message('stop')
        );
    }

    public function exit(int $exitCode = 0): void
    {
        echo "exit $exitCode";
    }
}
