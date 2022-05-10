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

namespace Chevere\Xr {
    use function Chevere\Filesystem\dirForPath;
    use Chevere\Writer\Interfaces\WriterInterface;
    use function Chevere\Writer\streamTemp;
    use Chevere\Writer\StreamWriter;
    use Chevere\Xr\Interfaces\XrInterface;
    use LogicException;
    use Throwable;

    /**
     * @codeCoverageIgnore
     */
    function getWriter(): WriterInterface
    {
        try {
            return WriterInstance::get();
        } catch (LogicException) {
            return new StreamWriter(streamTemp(''));
        }
    }

    function getXr(): XrInterface
    {
        try {
            return XrInstance::get();
        } catch (LogicException) {
            $xr = (new Xr())
                ->withConfigDir(
                    dirForPath(getcwd())
                );

            return (new XrInstance($xr))::get();
        }
    }

    /**
     * Register XR throwable handler.
     *
     * @param bool $callPrevious True to call the previous handler.
     * False to disable the previous handler.
     *
     * @codeCoverageIgnore
     */
    function registerThrowableHandler(bool $callPrevious = true): void
    {
        /** @var callable $xrHandler */
        $xrHandler = __NAMESPACE__ . '\\throwableHandler';
        $previous = set_exception_handler($xrHandler);
        if ($callPrevious === false || $previous === null) {
            return;
        }
        set_exception_handler(
            function (Throwable $throwable) use ($xrHandler, $previous) {
                $xrHandler($throwable);
                $previous($throwable);
            }
        );
    }

    /**
     * Handle a Throwable using XR.
     *
     * @param Throwable $throwable The throwable to handle
     * @param string $extra Extra contents to append to the XR message
     *
     * @codeCoverageIgnore
     */
    function throwableHandler(Throwable $throwable, string $extra = ''): void
    {
        if (getXr()->enable() === false) {
            return; // @codeCoverageIgnore
        }
        $parser = new ThrowableParser($throwable, $extra);
        getXr()->client()
            ->sendMessage(
                (new Message(
                    backtrace: $parser->throwableRead()->trace(),
                ))
                    ->withBody($parser->body())
                    ->withTopic($parser->topic())
                    ->withEmote($parser->emote())
            );
    }
}

namespace {
    use function Chevere\Xr\getWriter;
    use function Chevere\Xr\getXr;
    use Chevere\Xr\Inspector\Inspector;
    use Chevere\Xr\Inspector\InspectorInstance;
    use Chevere\Xr\Inspector\InspectorNull;
    use Chevere\Xr\Interfaces\InspectorInterface;
    use Chevere\Xr\Message;

// @codeCoverageIgnoreStart
    if (!defined('XR_BACKTRACE')) {
        define('XR_BACKTRACE', 1);
    }
    // @codeCoverageIgnoreEnd
    if (!function_exists('xr')) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to XR.
         *
         * ```php
         * xr($foo, $bar,...);
         * ```
         *
         * @param mixed ...$vars Variable(s) to dump
         * @param string $t Topic
         * @param string $e Emote
         * @param int $f `XR_BACKTRACE`
         */
        function xr(...$vars): void
        {
            if (getXr()->enable() === false) {
                return; // @codeCoverageIgnore
            }
            $defaultArgs = ['e' => '', 't' => '', 'f' => 0];
            $args = array_merge($defaultArgs, $vars);
            foreach (array_keys($defaultArgs) as $name) {
                if (array_key_exists($name, $vars)) {
                    unset($vars[$name]);
                }
            }
            getXr()->client()
                ->sendMessage(
                    (new Message(
                        backtrace: debug_backtrace(),
                    ))
                        ->withWriter(getWriter())
                        ->withVars(...$vars)
                        ->withTopic(strval($args['t']))
                        ->withEmote(strval($args['e']))
                        ->withFlags(intval($args['f']))
                );
        }
    }
    if (!function_exists('xrr')) { // @codeCoverageIgnore
        /**
         * Send a raw html message to XR.
         *
         * ```php
         * xrr($html, ...);
         * ```
         *
         * @param string $body Message to send
         * @param string $t Topic
         * @param string $e Emote
         * @param int $f `XR_BACKTRACE`
         *
         * @codeCoverageIgnore
         */
        function xrr(
            string $body,
            string $t = '',
            string $e = '',
            int $f = 0
        ): void {
            if (getXr()->enable() === false) {
                return;
            }
            getXr()->client()
                ->sendMessage(
                    (new Message(
                        backtrace: debug_backtrace(),
                    ))
                        ->withWriter(getWriter())
                        ->withBody($body)
                        ->withTopic($t)
                        ->withEmote($e)
                        ->withFlags($f)
                );
        }
    }
    if (!function_exists('xri')) { // @codeCoverageIgnore
        /**
         * Access XR inspector to send debug information.
         *
         * @codeCoverageIgnore
         */
        function xri(): InspectorInterface
        {
            try {
                return InspectorInstance::get();
            } catch (LogicException) {
                $xrInspector = getXr()->enable()
                    ? Inspector::class
                    : InspectorNull::class;
                $xrInspector = new $xrInspector(getXr()->client());

                return (new InspectorInstance($xrInspector))::get();
            }
        }
    }
}
