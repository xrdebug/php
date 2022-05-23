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

use Chevere\Xr\Interfaces\ClientInterface;
use Chevere\Xr\Interfaces\MessageInterface;
use Chevere\Xr\Traits\ClientTrait;

class ClientTester implements ClientInterface
{
    use ClientTrait;

    private MessageInterface $lastMessage;

    public function getLastMessage(): MessageInterface
    {
        return $this->lastMessage;
    }

    public function sendMessage(MessageInterface $message): void
    {
        $this->lastMessage = $message;
    }

    public function sendPause(MessageInterface $message): void
    {
        $this->lastMessage = $message;
    }
}
