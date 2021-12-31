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
        $args = [
            'f' => '',
            'a' => '',
            't' => '',
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
        $trace = debug_backtrace(0)[0];
        $body = [
            'body' => $stream->__toString(),
            'file_path' => $trace['file'] ?? '',
            'file_line' => $trace['line'] ?? '',
            'flair' => $args['f'],
            'action' => $args['a'],
            'topic' => $args['t'],
        ];
        $bodyString = http_build_query($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://0.0.0.0:9666/message');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyString);
        curl_exec($ch);
        curl_close($ch);
    }
}
