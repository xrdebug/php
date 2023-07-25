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
use Chevere\Xr\Client;
use Chevere\Xr\Curl;
use Chevere\Xr\Exceptions\StopException;
use Chevere\Xr\Message;
use phpseclib3\Crypt\EC;
use PHPUnit\Framework\TestCase;

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

    public function testCustom(): void
    {
        $port = 12345;
        $host = 'test-host';
        $isHttps = true;
        $key = EC::createKey('Ed25519');
        $client = new Client(port: $port, host: $host, isHttps: $isHttps, privateKey: $key);
        $this->assertSame(
            "https://{$host}:{$port}/endpoint",
            $client->getUrl('endpoint')
        );
        $message = new Message();
        $client->sendMessage($message);
        $this->assertFalse($client->isLocked($message->id()));
    }

    public function testWithCurl(): void
    {
        $curl = new Curl();
        $client = (new Client())->withCurl($curl);
        $this->assertSame($curl, $client->curl());
    }

    public function testPauseLocked()
    {
        require_once __DIR__ . '/src/CurlLockPauseTrue.php';
        $curl = new CurlLockPauseTrue();
        $client = (new Client())->withCurl($curl);
        $message = new Message();
        $this->assertTrue(
            $client->isLocked($message->id())
        );
    }

    public function testPauseStop()
    {
        require_once __DIR__ . '/src/CurlStopTrue.php';
        $curl = new CurlStopTrue();
        $client = (new Client())->withCurl($curl);
        $message = new Message();
        $this->expectException(StopException::class);
        $client->sendPause($message);
    }

    public function testPauseError()
    {
        require_once __DIR__ . '/src/CurlError.php';
        $curl = new CurlError();
        $client = (new Client())->withCurl($curl);
        $message = new Message();
        $client->sendPause($message);
        $this->assertFalse(
            $client->isLocked($message->id())
        );
    }
}
