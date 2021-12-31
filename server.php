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

require __DIR__ . '/vendor/autoload.php';

use Clue\React\Sse\BufferedChannel;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Middleware\LimitConcurrentRequestsMiddleware;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Middleware\StreamingRequestMiddleware;
use React\Stream\ThroughStream;

$loop = React\EventLoop\Loop::get();
$channel = new BufferedChannel();
$handler = function (ServerRequestInterface $request) use ($channel, $loop) {
    switch ($request->getUri()->getPath()) {
        case '/':
            return new Response(
                '200',
                ['Content-Type' => 'text/html'],
                file_get_contents(__DIR__ . '/index.html')
            );
        case '/icon.svg':
            return new Response(
                '200',
                ['Content-Type' => 'image/svg+xml'],
                file_get_contents(__DIR__ . '/icon.svg')
            );
        case '/icon.png':
            return new Response(
                '200',
                ['Content-Type' => 'image/png'],
                file_get_contents(__DIR__ . '/icon.png')
            );
        case '/style.css':
            return new Response(
                '200',
                ['Content-Type' => 'text/css'],
                file_get_contents(__DIR__ . '/style.css')
            );
        case '/message':
            if ($request->getMethod() !== 'POST') {
                return new Response(405);
            }
            $body = $request->getParsedBody() ?? [];
            $message = $body['body'] ?? '';
            $flair = $body['flair'] ?? '';
            $topic = $body['topic'] ?? '';
            if (($message . $flair . $topic) !== '') {
                $file = $body['file_path'] ?? '';
                $line = $body['file_line'] ?? '';
                $fileDisplay = $file;
                $fileDisplayShort = basename($file);
                if ($line !== '') {
                    $fileDisplay .= ':' . $line;
                    $fileDisplayShort .= ':' . $line;
                }
                $channel->writeMessage(
                    json_encode([
                        'message' => $message,
                        'file_path' => $file,
                        'file_line' => $line,
                        'file_display' => $fileDisplay,
                        'file_display_short' => $fileDisplayShort,
                        'flair' => $flair,
                        'action' => $body['action'] ?? '',
                        'topic' => $topic,
                    ])
                );
            }

            return new Response(
                '201',
                ['Content-Type' => 'text/json']
            );
        case '/dump':
            $stream = new ThroughStream();
            $id = $request->getHeaderLine('Last-Event-ID');
            $loop->futureTick(function () use ($channel, $stream, $id) {
                $channel->connect($stream, $id);
            });
            $serverParams = $request->getServerParams();
            $message = ['message' => 'New dump session started [' . $serverParams['REMOTE_ADDR'] . ']'];
            $channel->writeMessage(json_encode($message));
            $stream->on('close', function () use ($stream, $channel, $request, $serverParams) {
                $channel->disconnect($stream);
                $message = ['message' => 'Dump session end [' . $serverParams['REMOTE_ADDR'] . ']'];
                $channel->writeMessage(json_encode($message));
            });

            return new Response(
                200,
                ['Content-Type' => 'text/event-stream'],
                $stream
            );
        default:
            return new Response(404);
    }
};
$http = new React\Http\HttpServer(
    $loop,
    new StreamingRequestMiddleware(),
    new LimitConcurrentRequestsMiddleware(100),
    new RequestBodyBufferMiddleware(8 * 1024 * 1024),
    new RequestBodyParserMiddleware(100 * 1024, 1),
    $handler
);
$socket = new \React\Socket\SocketServer(
    uri: '0.0.0.0:' . ($argv[1] ?? '0'),
    context: [],
    loop: $loop
);
$http->listen($socket);
echo 'Server now listening on ' . $socket->getAddress() . ' [' . parse_url($socket->getAddress(), PHP_URL_PORT) . ']' . PHP_EOL;
$loop->run();
