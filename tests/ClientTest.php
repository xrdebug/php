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

namespace Chevere\Tests;

use Chevere\Tests\src\CurlError;
use Chevere\Tests\src\CurlLockPauseTrue;
use Chevere\Tests\src\CurlStopTrue;
use Chevere\xrDebug\PHP\Client;
use Chevere\xrDebug\PHP\Curl;
use Chevere\xrDebug\PHP\Exceptions\StopException;
use Chevere\xrDebug\PHP\Message;
use phpseclib3\Crypt\EC;
use PHPUnit\Framework\TestCase;
use function Chevere\xrDebug\PHP\sign;

final class ClientTest extends TestCase
{
    public function testDefault(): void
    {
        $client = new Client();
        $this->assertSame(
            'http://localhost:27420/endpoint',
            $client->getUrl('endpoint')
        );
    }

    public function testPost(): void
    {
        $port = 12345;
        $host = 'test-host';
        $isHttps = true;
        $scheme = $isHttps ? 'https' : 'http';
        $message = new Message();
        $options = [
            CURLINFO_HEADER_OUT => true,
            CURLOPT_ENCODING => '',
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($message->toArray()),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_URL => "{$scheme}://{$host}:{$port}/messages",
            CURLOPT_USERAGENT => 'xrdebug/php',
        ];
        $curl = $this->createMock(Curl::class);
        $curl->expects($this->exactly(2))
            ->method('setOptArray')
            ->with($options);
        $client = new Client(curl: $curl, port: $port, host: $host, isHttps: $isHttps);
        $this->assertSame($curl, $client->curl());
        $client->sendMessage($message);
        $this->assertSame($options, $client->options());
        $client->sendMessage($message);
    }

    public function testCustom(): void
    {
        $port = 12345;
        $host = 'test-host';
        $isHttps = true;
        $key = EC::createKey('Ed25519');
        $client = new Client(
            port: $port,
            host: $host,
            isHttps: $isHttps,
            privateKey: $key
        );
        $this->assertSame(
            "https://{$host}:{$port}/endpoint",
            $client->getUrl('endpoint')
        );
        $message = new Message();
        $client->sendMessage($message);
        $signatureDisplay = sign($key, $message->toArray());
        $options = [
            CURLOPT_HTTPHEADER => [
                "X-Signature: {$signatureDisplay}",
            ],
        ];
        $result = [
            CURLOPT_HTTPHEADER => $client->options()[CURLOPT_HTTPHEADER],
        ];
        $this->assertSame($options, $result);
        $this->assertFalse($client->isPaused($message->id()));
    }

    public function testPauseLocked()
    {
        $curl = new CurlLockPauseTrue();
        $client = new Client(curl: $curl);
        $message = new Message();
        $this->assertTrue(
            $client->isPaused($message->id())
        );
    }

    public function testPauseStop()
    {
        $curl = new CurlStopTrue();
        $client = new Client(curl: $curl);
        $message = new Message();
        $this->expectException(StopException::class);
        $client->sendPause($message);
    }

    public function testPauseError()
    {
        $curl = new CurlError();
        $client = new Client(curl: $curl);
        $message = new Message();
        $client->sendPause($message);
        $this->assertFalse(
            $client->isPaused($message->id())
        );
    }
}
