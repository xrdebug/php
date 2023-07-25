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

use Chevere\Xr\Curl;
use CurlHandle;
use PHPUnit\Framework\TestCase;

final class CurlTest extends TestCase
{
    public function testError(): void
    {
        $curl = new Curl();
        $this->assertSame('', $curl->error());
    }

    public function testExec(): void
    {
        $curl = new Curl();
        $this->assertFalse($curl->exec());
    }

    public function testOptArray(): void
    {
        $this->expectNotToPerformAssertions();
        $curl = new Curl();
        $curl->setOptArray([
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);
    }

    public function testNoHandleClose(): void
    {
        $this->expectNotToPerformAssertions();
        $curl = new Curl();
        $curl->close();
    }

    public function testInitNull(): void
    {
        $curl = new Curl();
        $handle = $curl->handle();
        $this->assertInstanceOf(CurlHandle::class, $handle);
        $this->assertSame('', $curl->error());
        $curl->close();
    }

    public function testExecBool(): void
    {
        $curl = new Curl();
        $this->assertFalse($curl->exec());
        $curl = new Curl('https://www.cloudflare.com/ips-v4');
        $this->expectOutputRegex('#.*#');
        $this->assertTrue($curl->exec());
        $curl->close();
    }

    public function testExecString(): void
    {
        $curl = new Curl('https://www.cloudflare.com/ips-v4');
        $curl->setOptArray([
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        $this->assertIsString($curl->exec());
    }
}
