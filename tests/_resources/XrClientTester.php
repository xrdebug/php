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

use Chevere\Xr\Interfaces\XrClientInterface;
use Chevere\Xr\Interfaces\XrMessageInterface;
use Chevere\Xr\Traits\XrClientTrait;

class XrClientTester implements XrClientInterface
{
    use XrClientTrait;

    private XrMessageInterface $lastMessage;

    public function getLastMessage(): XrMessageInterface
    {
        return $this->lastMessage;
    }

    public function sendMessage(XrMessageInterface $message): void
    {
        $this->lastMessage = $message;
    }

    public function sendPause(XrMessageInterface $message): void
    {
        $this->lastMessage = $message;
    }
}
