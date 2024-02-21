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

use Chevere\xrDebug\PHP\Interfaces\ClientInterface;
use Chevere\xrDebug\PHP\Interfaces\MessageInterface;
use Chevere\xrDebug\PHP\Traits\ClientTrait as TraitsClientTrait;

class ClientTester implements ClientInterface
{
    use TraitsClientTrait;

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
