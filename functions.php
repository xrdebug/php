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
use Chevere\Components\Writer\StreamWriter;
use Chevere\Xr\Components\VarDump\Outputters\VarDumpHtmlOutputter;
use function RingCentral\Psr7\stream_for;

if (!function_exists('xr')) {
    /**
     * Dumps information about one or more variables to XR
     */
    function xr(...$vars): void
    {
        $stream = stream_for('');
        (new VarDump(
            new VarDumpHtmlFormatter(),
            new VarDumpHtmlOutputter()
        ))
            ->withShift(1)
            ->withVars(...$vars)
            ->process(new StreamWriter($stream));
        $message = $stream->__toString();
        $trace = debug_backtrace(0)[0];
        $fileBasename = $trace['file'];
        $fileLine = $trace['line'];
        $body = ['body' => $message, 'filePath' => "$fileBasename:$fileLine"];
        $bodyString = http_build_query($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://0.0.0.0:9666/message');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyString);
        curl_exec($ch);
        curl_close($ch);
    }
}
