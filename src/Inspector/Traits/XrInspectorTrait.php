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

use Chevere\Xr\Interfaces\XrInterface;
use Chevere\Xr\XrMessage;

trait XrInspectorTrait
{
    public function __construct(protected XrInterface $xr)
    {
    }
    
    private function sendMessage(
        string $body,
        string $topic,
        string $emote,
        int $flags
    ): void {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        array_shift($backtrace);
        $subTopic = $backtrace[0]['function'];
        $withTopic = [$subTopic];
        if ($topic !== '') {
            $withTopic[] = $topic;
        }
        $this->xr->client()
            ->sendMessage(
                (new XrMessage(
                    backtrace: $backtrace,
                ))
                    ->withBody($body)
                    ->withTopic(implode('»', $withTopic))
                    ->withEmote($emote)
                    ->withFlags($flags)
            );
    }
}
