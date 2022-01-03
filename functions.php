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

use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerHtmlFormatter;
use Chevere\Components\ThrowableHandler\ThrowableTraceFormatter;
use Chevere\Components\VarDump\Formatters\VarDumpHtmlFormatter;
use Chevere\Components\VarDump\VarDump;
use function Chevere\Components\Writer\streamFor;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Xr\Components\VarDump\Outputters\VarDumpHtmlOutputter;

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
        $backtrace = debug_backtrace();
        $caller = $backtrace[0];
        $defaultArgs = [
            'e' => '',
            't' => '',
            'f' => 0,
        ];
        $args = array_merge($defaultArgs, $vars);
        $args = [
            'e' => (string) $args['e'],
            't' => (string) $args['t'],
            'f' => (int) $args['f'],
        ];
        foreach ($args as $name => &$value) {
            if (array_key_exists($name, $vars)) {
                if (is_string($vars[$name])) {
                    $value = $vars[$name];
                }
                unset($vars[$name]);
            }
        }
        $stream = streamFor('php://temp', 'r+');
        (new VarDump(
            new VarDumpHtmlFormatter(),
            new VarDumpHtmlOutputter()
        ))
            ->withShift(1)
            ->withVars(...$vars)
            ->process(new StreamWriter($stream));
        $body = '<div class="dump">' . $stream->__toString() . '</div>';
        if ($args['f'] & XR_BACKTRACE) {
            $traceFormatter = new ThrowableTraceFormatter($backtrace, new ThrowableHandlerHtmlFormatter());
            $body .= '<div class="backtrace">' . $traceFormatter->toString() . '</div>';
        }
        $data = [
            'body' => $body,
            'file_path' => $caller['file'] ?? '',
            'file_line' => $caller['line'] ?? '',
            'emote' => $args['e'],
            'topic' => $args['t'],
            'pause' => (string) (bool) ($args['f'] & XR_PAUSE),
        ];
        $bodyString = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://0.0.0.0:9666/message');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyString);
        curl_exec($ch);
        curl_close($ch);
    }
}
