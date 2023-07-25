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
    use Chevere\Writer\Interfaces\WriterInterface;
    use Chevere\Writer\StreamWriter;
    use Chevere\Xr\Interfaces\XrInterface;
    use LogicException;
    use Throwable;
    use function Chevere\Filesystem\directoryForPath;
    use function Chevere\Writer\streamTemp;
    use function Safe\getcwd;

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
                    directoryForPath(
                        getcwd()
                    )
                );

            return (new XrInstance($xr))::get();
        }
    }

    /**
     * @codeCoverageIgnore
     */
    function getXrFailover(): ?XrInterface
    {
        try {
            return getXr();
        } catch (Throwable $e) {
            $caller = debug_backtrace(0, 2)[1];
            $file = $caller['file'] ?? '@unknown';
            $line = strval($caller['line'] ?? 0);
            error_log(
                strtr(
                    'Unable to use XR Debug at %s: %e',
                    [
                        '%s' => "{$file}:{$line}",
                        '%e' => $e->getMessage(),
                    ]
                )
            );

            return null;
        }
    }

    /**
     * Register XR Debug throwable handler.
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
     * Handle a Throwable using XR Debug.
     *
     * @param Throwable $throwable The throwable to handle
     * @param string $extra Extra contents to append to the XR Debug message
     *
     * @codeCoverageIgnore
     */
    function throwableHandler(Throwable $throwable, string $extra = ''): void
    {
        $xr = getXrFailover();
        if ($xr === null || $xr->isEnabled() === false) {
            return; // @codeCoverageIgnore
        }
        $parser = new ThrowableParser($throwable, $extra);
        $xr->client()
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
    use Chevere\Xr\Inspector\Inspector;
    use Chevere\Xr\Inspector\InspectorInstance;
    use Chevere\Xr\Inspector\InspectorNull;
    use Chevere\Xr\Interfaces\InspectorInterface;
    use Chevere\Xr\Message;
    use function Chevere\Xr\getWriter;
    use function Chevere\Xr\getXrFailover;

    // @codeCoverageIgnoreStart
    if (! defined('XR_BACKTRACE')) {
        define('XR_BACKTRACE', 1);
    }
    // @codeCoverageIgnoreEnd
    if (! function_exists('xr')) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to XR Debug.
         *
         * ```php
         * xr($foo, $bar,...);
         * ```
         *
         * @param mixed $vars Variables to dump
         */
        function xr(mixed ...$vars): void
        {
            $xr = getXrFailover();
            if ($xr === null || $xr->isEnabled() === false) {
                return; // @codeCoverageIgnore
            }
            $defaultArgs = [
                'e' => '',
                't' => '',
                'f' => 0,
            ];
            /** @var array<string|int, string> */
            $args = array_merge($defaultArgs, $vars);
            foreach (array_keys($defaultArgs) as $name) {
                if (array_key_exists($name, $vars)) {
                    unset($vars[$name]);
                }
            }
            $xr->client()
                ->sendMessage(
                    (new Message(
                        backtrace: debug_backtrace(),
                    ))
                        ->withWriter(getWriter())
                        ->withVariables(...$vars)
                        ->withTopic(strval($args['t']))
                        ->withEmote(strval($args['e']))
                        ->withFlags(intval($args['f']))
                );
        }
    }
    if (! function_exists('xrr')) { // @codeCoverageIgnore
        /**
         * Send a raw html message to XR Debug.
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
            $xr = getXrFailover();
            if ($xr === null || $xr->isEnabled() === false) {
                return;
            }
            $xr->client()
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
    if (! function_exists('xri')) { // @codeCoverageIgnore
        /**
         * Access XR Debug inspector to send debug information.
         *
         * @codeCoverageIgnore
         */
        function xri(): InspectorInterface
        {
            $xr = getXrFailover();
            if ($xr === null) {
                return new InspectorNull();
            }

            try {
                return InspectorInstance::get();
            } catch (LogicException) {
                $xrInspector = $xr->isEnabled()
                    ? new Inspector($xr->client())
                    : new InspectorNull();

                return (new InspectorInstance($xrInspector))::get();
            }
        }
    }
}
