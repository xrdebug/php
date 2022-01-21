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
    use Chevere\Components\ThrowableHandler\ThrowableRead;
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

    function xrThrowableHandler(Throwable $throwable): void
    {
        $readFirst = new ThrowableRead($throwable);
        $backtrace = $readFirst->trace();
        $topic = substr(
            $readFirst->className(),
            strrpos($readFirst->className(), '\\') + 1
        );
        $message = '';
        do {
            $throwableRead = new ThrowableRead($throwable);
            
            $message .= $throwableRead->className() . ' thrown '
                . $throwableRead->code() . "\n";
            $message .= $throwable->getMessage() . ' in '
                . $throwableRead->file() . ':' . $throwableRead->line();
            if ($throwable->getPrevious() !== null) {
                $message .= '<br>-->Previous: ';
            }
        } while ($throwable = $throwable->getPrevious());

        if (getXrInstance()->enable() === false) {
            return; // @codeCoverageIgnore
        }
        $emote = '⚠️';
        $flags = XR_BACKTRACE;
        getXrInstance()->client()
            ->sendMessage(
                (new Message(
                    writer: getWriter(),
                    vars: [$message],
                    backtrace: $backtrace,
                ))
                    ->withTopic($topic)
                    ->withEmote($emote)
                    ->withFlags($flags)
            );

        $template = [
            '<div class="throwable">',
            '   <h2>%title%</h2>',
            '   <p>%message%</p>',
            '</div>'
        ];
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
         * @param string $t Message Topic
         * @param string $e Message Emote
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
            $topic = (string) $args['t'];
            $emote = (string) $args['e'];
            $flags = (int) $args['f'];
            getXrInstance()->client()
                ->sendMessage(
                    (new Message(
                        writer: getWriter(),
                        vars: $vars,
                        backtrace: debug_backtrace(),
                    ))
                        ->withTopic($topic)
                        ->withEmote($emote)
                        ->withFlags($flags)
                );
        }
    }
}