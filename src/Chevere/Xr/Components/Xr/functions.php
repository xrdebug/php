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

use function Chevere\Components\Writer\streamFor;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Xr\Components\Xr\Client;
use Chevere\Xr\Components\Xr\Message;

// @codeCoverageIgnoreStart

if (!defined('XR_BACKTRACE')) {
    define('XR_BACKTRACE', 1);
}
if (!defined('XR_PAUSE')) {
    define('XR_PAUSE', 2);
}

if (!function_exists('xr')) {
    /**
     * Dumps information about one or more variables to XR.
     *
     * ```php
     * xr($foo, $bar,...);
     * ```
     *
     * @param mixed ...$vars Variable(s) to dump
     * @param string $t Message Topic
     * @param string $e Message Emote
     * @param int $f `XR_BACKTRACE | XR_PAUSE`
     */
    function xr(...$vars): void
    {
        $defaultArgs = ['e' => '', 't' => '', 'f' => 0];
        $args = array_merge($defaultArgs, $vars);
        foreach (array_keys($defaultArgs) as $name) {
            if (array_key_exists($name, $vars)) {
                unset($vars[$name]);
            }
        }
        $topic = (string) $args['t'];
        $emote = (string) $args['e'];
        $flags = (int) $args['f'];
        $message = new Message(
            writer: new StreamWriter(streamFor('php://temp', 'r+')),
            vars: $vars,
            shift: 1,
        );
        if ($topic !== '') {
            $message = $message->withTopic($topic);
        }
        if ($emote !== '') {
            $message = $message->withEmote($emote);
        }
        if ($flags !== 0) {
            $message = $message->withFlags($flags);
        }
        (new Client())
            ->sendMessage($message);
    }
}
// @codeCoverageIgnoreEnd
