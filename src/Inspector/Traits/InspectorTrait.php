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

namespace Chevere\xrDebug\PHP\Inspector\Traits;

use Chevere\xrDebug\PHP\Interfaces\ClientInterface;
use Chevere\xrDebug\PHP\Message;
use Throwable;

trait InspectorTrait
{
    public function __construct(
        protected ClientInterface $client,
    ) {
    }

    public function pause(
        string $t = '',
        string $e = '',
        int $f = 0,
    ): void {
        $this->sendCommand(
            command: 'pause',
            topic: $t,
            emote: $e,
            flags: $f,
        );
    }

    public function memory(
        string $t = '',
        string $e = '',
        int $f = 0,
    ): void {
        $memory = memory_get_usage(true);
        $this->sendCommand(
            command: 'message',
            body: sprintf('%.2F MB', $memory / 1000000),
            topic: $t,
            emote: $e,
            flags: $f,
        );
    }

    private function sendCommand(
        string $command,
        string $body = '',
        string $topic = '',
        string $emote = '',
        int $flags = 0
    ): void {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        array_shift($backtrace);
        $message = (new Message(
            backtrace: $backtrace,
        ))
            ->withBody($body)
            ->withTopic($topic)
            ->withEmote($emote)
            ->withFlags($flags);
        $command = 'send' . ucfirst($command);

        try {
            $this->client->{$command}($message);
        } catch (Throwable $e) {
            if (PHP_SAPI === 'cli') {
                echo '* ' . $e->getMessage() . PHP_EOL;
                $this->client->exit(255);
            }
        }
    }
}
