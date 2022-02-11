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

use Chevere\Xr\XrClient;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testDefault(): void
    {
        $client = new XrClient();
        $this->assertSame(
            'http://localhost:27420/endpoint',
            $client->getUrl('endpoint')
        );
    }

    public function testCustom(): void
    {
        $port = 12345;
        $host = 'test-host';
        $client = new XrClient(port: $port, host: $host);
        $this->assertSame(
            "http://$host:$port/endpoint",
            $client->getUrl('endpoint')
        );
    }
}
