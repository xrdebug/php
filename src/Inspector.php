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

namespace Chevere\xrDebug\PHP;

use Chevere\xrDebug\PHP\Interfaces\ClientInterface;
use Chevere\xrDebug\PHP\Interfaces\InspectorInterface;
use Throwable;

class Inspector implements InspectorInterface
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
        $this->command(
            action: 'sendPause',
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
        $body = sprintf('%.2F MB', $memory / 1000000);
        $this->command(
            action: 'sendMessage',
            body: $body,
            topic: $t,
            emote: $e,
            flags: $f,
        );
    }

    protected function command(
        string $action,
        string $body = '',
        string $topic = '',
        string $emote = '',
        int $flags = 0
    ): void {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        array_shift($backtrace);
        $message = new Message(backtrace: $backtrace);
        $message = $message
            ->withBody($body)
            ->withTopic($topic)
            ->withEmote($emote)
            ->withFlags($flags);

        try {
            $this->client->{$action}($message);
        } catch (Throwable $e) {
            if (PHP_SAPI === 'cli') {
                echo <<<PLAIN
                * {$e->getMessage()}

                PLAIN;
                $this->client->exit(255);
            }
        }
    }
}
