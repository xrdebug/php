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
use React\Stream\ThroughStream;

$loop = React\EventLoop\Loop::get();
$channel = new BufferedChannel();
$http = new React\Http\HttpServer($loop, function (ServerRequestInterface $request) use ($channel, $loop) {
    switch ($request->getUri()->getPath()) {
        case '/':
            return new Response(
                '200',
                ['Content-Type' => 'text/html'],
                file_get_contents(__DIR__ . '/index.html')
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
            $parsedBody = $request->getParsedBody() ?? [];
            $message = $parsedBody['body'] ?? '';
            $filePath = $parsedBody['filePath'] ?? '';
            if ($message !== '') {
                $data = ['message' => $message, 'filePath' => $filePath];
                $channel->writeMessage(json_encode($data));
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
});
$socket = new \React\Socket\SocketServer(
    uri: '0.0.0.0:' . ($argv[1] ?? '0'),
    context: [],
    loop: $loop
);
$http->listen($socket);
echo 'Server now listening on ' . $socket->getAddress() . ' [' . parse_url($socket->getAddress(), PHP_URL_PORT) . ']' . PHP_EOL;
$loop->run();
