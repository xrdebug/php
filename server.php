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

foreach (['/', '/../../../'] as $path) {
    $autoload = __DIR__ . $path . 'vendor/autoload.php';
    if (stream_resolve_include_path($autoload)) {
        require $autoload;

        break;
    }
}

use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerConsoleDocument;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use function Chevere\Components\ThrowableHandler\throwableHandler;
use function Chevere\Components\Writer\streamFor;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Components\Writer\Writers;
use function Chevere\Components\Writer\writers;
use Chevere\Components\Writer\WritersInstance;
use Clue\React\Sse\BufferedChannel;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Http\Middleware\LimitConcurrentRequestsMiddleware;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Middleware\StreamingRequestMiddleware;
use React\Stream\ThroughStream;
use samejack\PHP\ArgvParser;

new WritersInstance(
    (new Writers())
        ->with(
            new StreamWriter(
                streamFor('php://output', 'w')
            )
        )
        ->withError(
            new StreamWriter(
                streamFor('php://stderr', 'w')
            )
        )
);
set_error_handler(ThrowableHandler::ERRORS_AS_EXCEPTIONS);
register_shutdown_function(ThrowableHandler::FATAL_ERROR_HANDLER);
set_exception_handler(function (Throwable $e) {
    $handler = throwableHandler($e);
    $docInternal = new ThrowableHandlerConsoleDocument($handler);
    writers()->error()
        ->write($docInternal->__toString() . "\n");
    die(255);
});

$loop = Loop::get();
$channel = new BufferedChannel();
$handler = function (ServerRequestInterface $request) use ($channel, $loop) {
    switch ($request->getUri()->getPath()) {
        case '/':
            return new Response(
                '200',
                ['Content-Type' => 'text/html'],
                file_get_contents(__DIR__ . '/asset/index.html')
            );
        case '/app.js':
            return new Response(
                '200',
                ['Content-Type' => 'text/javascript'],
                file_get_contents(__DIR__ . '/asset/app.js')
            );
        case '/icon.svg':
            return new Response(
                '200',
                ['Content-Type' => 'image/svg+xml'],
                file_get_contents(__DIR__ . '/asset/icon.svg')
            );
        case '/icon.png':
            return new Response(
                '200',
                ['Content-Type' => 'image/png'],
                file_get_contents(__DIR__ . '/asset/icon.png')
            );
        case '/style.css':
            return new Response(
                '200',
                ['Content-Type' => 'text/css'],
                file_get_contents(__DIR__ . '/asset/style.css')
            );
        case '/fonts/firacode/firacode-regular.woff':
            return new Response(
                '200',
                ['Content-Type' => 'font/woff'],
                file_get_contents(__DIR__ . '/asset/fonts/firacode/firacode-regular.woff')
            );
        case '/message':
            if ($request->getMethod() !== 'POST') {
                return new Response(405);
            }
            $body = $request->getParsedBody() ?? [];
            $message = $body['body'] ?? '';
            $emote = $body['emote'] ?? '';
            $topic = $body['topic'] ?? '';
            if (($message . $emote . $topic) !== '') {
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
                        'emote' => $emote,
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
$http = new HttpServer(
    $loop,
    new StreamingRequestMiddleware(),
    new LimitConcurrentRequestsMiddleware(100),
    new RequestBodyBufferMiddleware(8 * 1024 * 1024),
    new RequestBodyParserMiddleware(100 * 1024, 1),
    $handler
);
$options = (new ArgvParser())->parseConfigs();
if (array_key_exists('h', $options) || array_key_exists('help', $options)) {
    echo implode("\n", ['-p Port (default 27420)', '-c Cert .pem file', '']);
    die(0);
}
$host = '0.0.0.0';
$port = $options['p'] ?? '0';
$cert = $options['c'] ?? null;
$scheme = isset($cert) ? 'tls' : 'tcp';
$uri = "$scheme://$host:$port";
$context = $scheme === 'tcp'
    ? []
    : [
        'tls' => [
            'local_cert' => $cert
        ]
    ];
$socket = new \React\Socket\SocketServer(
    uri: $uri,
    context: $context,
    loop: $loop
);
$http->listen($socket);
$socket->on('error', 'printf');
$scheme = parse_url($socket->getAddress(), PHP_URL_SCHEME);
$httpAddress = strtr($socket->getAddress(), ['tls:' => 'https:', 'tcp:' => 'http:']);
echo "Chevere XR debugger listening on ($scheme) $httpAddress" . PHP_EOL;
$loop->run();
