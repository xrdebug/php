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

namespace Chevere\Xr\Components\Xr {
    use function Chevere\Components\Writer\streamTemp;
    use Chevere\Components\Writer\StreamWriter;
    use Chevere\Interfaces\Writer\WriterInterface;
    use LogicException;

    function getWriter(): WriterInterface
    {
        try {
            return WriterInstance::get();
        } catch (LogicException $e) {
            return new StreamWriter(streamTemp(''));
        }
    }
}

namespace {
    use Chevere\Xr\Components\Xr\Client;
    use function Chevere\Xr\Components\Xr\getWriter;
    use Chevere\Xr\Components\Xr\Message;

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
            (new Client())->sendMessage(
                (new Message(
                    writer: getWriter(),
                    vars: $vars,
                    shift: 1,
                ))
                    ->withTopic($topic)
                    ->withEmote($emote)
                    ->withFlags($flags)
            );
        }
    }
}
