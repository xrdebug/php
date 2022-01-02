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

if (!function_exists('xr')) {
    /**
     * Dumps information about one or more variables to XR.
     */
    function xr(...$vars): void
    {
        $backtrace = debug_backtrace();
        $defaultArgs = [
            'f' => '',
            't' => '',
            'b' => false,
            'p' => false,
        ];
        $args = array_merge($defaultArgs, $vars);
        $args = [
            'f' => (string) $args['f'],
            't' => (string) $args['t'],
            'b' => (bool) $args['b'],
            'p' => (bool) $args['p'],
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
        $body = $stream->__toString();
        if ($args['b']) {
            $traceFormatter = new ThrowableTraceFormatter($backtrace, new ThrowableHandlerHtmlFormatter());
            $body .= '<div class="backtrace">' . $traceFormatter->toString() . '</div>';
        }
        $trace = $backtrace[0];
        $data = [
            'body' => $body,
            'file_path' => $trace['file'] ?? '',
            'file_line' => $trace['line'] ?? '',
            'flair' => $args['f'],
            'topic' => $args['t'],
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
