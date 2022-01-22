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
    use function Chevere\Components\Filesystem\dirForPath;
    use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerHtmlFormatter;
    use Chevere\Components\ThrowableHandler\ThrowableRead;
    use Chevere\Components\ThrowableHandler\ThrowableTraceFormatter;
use function Chevere\Components\Writer\streamTemp;
    use Chevere\Components\Writer\StreamWriter;
    use Chevere\Interfaces\Writer\WriterInterface;
    use LogicException;
    use Throwable;

    /**
     * @codeCoverageIgnore
     */
    function getWriter(): WriterInterface
    {
        try {
            return WriterInstance::get();
        } catch (LogicException $e) {
            return new StreamWriter(streamTemp(''));
        }
    }

    function getXrInstance(): Xr
    {
        try {
            return XrInstance::get();
        } catch (LogicException $e) {
            return new Xr(dirForPath(getcwd()));
        }
    }

    /**
     * @codeCoverageIgnore
     */
    function registerXrThrowableHandler(bool $callPrevious = true): void
    {
        $xrHandler = __NAMESPACE__ . '\\XrThrowableHandler';
        $previous = set_exception_handler($xrHandler);
        if ($callPrevious === false) {
            return;
        }
        set_exception_handler(function (Throwable $e) use ($xrHandler, $previous) {
            $xrHandler($e);
            if (is_callable($previous)) {
                $previous($e);
            }
        });
    }

    /**
     * Handles a Throwable using XR.
     *
     * @param Throwable $throwable The throwable to handle
     * @param string $extra Extra contents to append to the XR message
     *
     * @codeCoverageIgnore
     */
    function xrThrowableHandler(Throwable $throwable, string ...$extra): void
    {
        if (getXrInstance()->enable() === false) {
            return; // @codeCoverageIgnore
        }
        $readFirst = new ThrowableRead($throwable);
        $backtrace = $readFirst->trace();
        $formatter = new ThrowableHandlerHtmlFormatter();
        $topic = basename(
            str_replace('\\', DIRECTORY_SEPARATOR, $readFirst->className())
        );
        $body = '<div class="throwable">';
        $template = '<div class="throwable-item">
<h2 class="throwable-title">%title%</h2>
<div class="throwable-code">%code%</div>
<div class="throwable-message">%message%</div>
%extra%
<div class="throwable-backtrace backtrace">%trace%</div>
</div>';
        $appendExtra = implode(
            '',
            array_map(
                function (string $extra): string {
                    return $extra;
                },
                $extra
            )
        );
        $aux = 0;
        do {
            $aux++;
            if ($aux === 1) {
                $throwableRead = $readFirst;
                $trace = $backtrace;
            } else {
                $throwableRead = new ThrowableRead($throwable);
                $trace = [
                [
                    'function' => '{main}',
                    'file' => $throwableRead->file(),
                    'line' => $throwableRead->line(),
                ]
            ];
            }
            $traceFormatter = new ThrowableTraceFormatter($trace, $formatter);
            $translate = [
                '%title%' => $throwableRead->className(),
                '%code%' => $throwableRead->code(),
                '%message%' => $throwableRead->message(),
                '%extra%' => $aux === 1 ? $appendExtra : '',
                '%trace%' => $traceFormatter->__toString(),
            ];
            $body .= strtr($template, $translate);
        } while ($throwable = $throwable->getPrevious());
        $body .= '</div>';
        $emote = '⧱';
        getXrInstance()->client()
            ->sendMessage(
                (new Message(
                    backtrace: $backtrace,
                ))
                    ->withBody($body)
                    ->withTopic($topic)
                    ->withEmote($emote)
            );
    }
}

namespace {
    use function Chevere\Xr\getWriter;
    use function Chevere\Xr\getXrInstance;
    use Chevere\Xr\Message;

// @codeCoverageIgnoreStart
    if (!defined('XR_BACKTRACE')) {
        define('XR_BACKTRACE', 1);
    }
    if (!defined('XR_PAUSE')) {
        define('XR_PAUSE', 2);
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
         * @param int $f `XR_BACKTRACE | XR_PAUSE`
         */
        function xr(...$vars): void
        {
            if (getXrInstance()->enable() === false) {
                return; // @codeCoverageIgnore
            }
            $defaultArgs = ['e' => '', 't' => '', 'f' => 0];
            $args = array_merge($defaultArgs, $vars);
            foreach (array_keys($defaultArgs) as $name) {
                if (array_key_exists($name, $vars)) {
                    unset($vars[$name]);
                }
            }
            getXrInstance()->client()
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
         * Send a raw message to XR.
         *
         * ```php
         * xrr($message, ...);
         * ```
         *
         * @param string $message Message to send
         * @param string $t Topic
         * @param string $e Emote
         * @param int $f `XR_BACKTRACE | XR_PAUSE`
         *
         * @codeCoverageIgnore
         */
        function xrr(
            string $message,
            string $t = '',
            string $e = '',
            int $f = 0
        ): void {
            if (getXrInstance()->enable() === false) {
                return;
            }
            getXrInstance()->client()
                ->sendMessage(
                    (new Message(
                        backtrace: debug_backtrace(),
                    ))
                        ->withBody($message)
                        ->withTopic($t)
                        ->withEmote($e)
                        ->withFlags($f)
                );
        }
    }
}
