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

use function Chevere\Filesystem\dirForPath;
use Chevere\Xr\XrBuild;
use PHPUnit\Framework\TestCase;

final class XrBuildTest extends TestCase
{
    public function testConstruct(): void
    {
        $build = new XrBuild(
            dirForPath(__DIR__ . '/_resources/app/src'),
            '6.6.6',
            'ElNúmeroDeLaBestia'
        );
        $this->assertStringEqualsFile(
            __DIR__ . '/_resources/app/build/index.html',
            $build->html()
        );
    }
}
