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
namespace Chevere\Xr\Inspector\Traits;

use Chevere\Xr\Exceptions\XrStopException;
use Chevere\Xr\Interfaces\XrInterface;
use Chevere\Xr\XrMessage;
use Chevere\Xr\XrPause;

trait XrInspectorTrait
{
    public function __construct(protected XrInterface $xr)
    {
    }

    public function pause(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $message = (new XrMessage(
            backtrace: $backtrace,
        ))
            ->withTopic($t)
            ->withEmote($e)
            ->withFlags($f);
        $pause = new XrPause($message);

        try {
            $this->xr->client()->sendPause($pause);
        } catch (XrStopException $e) {
            if (PHP_SAPI === 'cli') {
                echo '* ' . $e->getMessage() . PHP_EOL;
                die(255);
            }
        }
    }

    public function memory(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        $memory = memory_get_usage(true);
        $this->sendMessage(
            body: sprintf('%.2F MB', $memory / 1000000),
            topic: $t,
            emote: $e,
            flags: $f,
        );
    }
    
    private function sendMessage(
        string $body = '',
        string $topic = '',
        string $emote = '',
        int $flags = 0
    ): void {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        array_shift($backtrace);
        $message = (new XrMessage(
            backtrace: $backtrace,
        ))
            ->withBody($body)
            ->withTopic($topic)
            ->withEmote($emote)
            ->withFlags($flags);
        
        $this->xr->client()->sendMessage($message);
    }
}
