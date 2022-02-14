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

namespace Chevere\Xr\Tests;

use Chevere\Xr\XrCurl;
use CurlHandle;
use PHPUnit\Framework\TestCase;

final class XrCurlTest extends TestCase
{
    public function testError(): void
    {
        $curl = new XrCurl();
        $this->assertSame('', $curl->error());
    }

    public function testExec(): void
    {
        $curl = new XrCurl();
        $this->assertFalse($curl->exec());
    }

    public function testOptArray(): void
    {
        $this->expectNotToPerformAssertions();
        $curl = new XrCurl();
        $curl->setOptArray([CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1]);
    }

    public function testNoHandleClose(): void
    {
        $this->expectNotToPerformAssertions();
        $curl = new XrCurl();
        $curl->close();
    }
    
    public function testInitNull(): void
    {
        $curl = new XrCurl();
        $handle = $curl->handle();
        $this->assertInstanceOf(CurlHandle::class, $handle);
        $this->assertSame('', $curl->error());
        $curl->close();
    }

    public function testExecBool(): void
    {
        $curl = new XrCurl();
        $this->assertFalse($curl->exec());
        $curl = new XrCurl('https://www.cloudflare.com/ips-v4');
        $this->expectOutputRegex('#.*#');
        $this->assertTrue($curl->exec());
        $curl->close();
    }

    public function testExecString(): void
    {
        $curl = new XrCurl('https://www.cloudflare.com/ips-v4');
        $curl->setOptArray([CURLOPT_RETURNTRANSFER => 1]);
        $this->assertIsString($curl->exec());
    }
}
