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

use Chevere\Xr\Client;
use Chevere\Xr\Inspector\InspectorNull;
use PHPUnit\Framework\TestCase;

final class XrInspectorNullTest extends TestCase
{
    public function testConstruct(): void
    {
        $client = new Client();
        $inspector = new InspectorNull($client);
        foreach (['memory', 'pause'] as $method) {
            $this->assertNull($inspector->$method());
        }
    }
}
