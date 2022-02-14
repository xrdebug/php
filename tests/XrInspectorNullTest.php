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

use Chevere\Xr\Inspector\XrInspectorNull;
use Chevere\Xr\XrClient;
use PHPUnit\Framework\TestCase;

final class XrInspectorNullTest extends TestCase
{
    public function testConstruct(): void
    {
        $client = new XrClient();
        $inspector = new XrInspectorNull($client);
        foreach (['memory', 'pause'] as $method) {
            $this->assertNull($inspector->$method());
        }
    }
}
