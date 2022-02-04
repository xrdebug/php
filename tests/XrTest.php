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

namespace Chevere\Xr\Tests\Chevere\Xr;

use function Chevere\Filesystem\dirForPath;
use Chevere\Xr\Client;
use Chevere\Xr\Xr;
use PHPUnit\Framework\TestCase;

final class XrTest extends TestCase
{
    public function testConstructWithoutSettingsFile(): void
    {
        $xr = new Xr(dirForPath(__DIR__));
        $this->assertSame(true, $xr->enable());
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithoutSettingsFileSubfolder(): void
    {
        $xr = new Xr(dirForPath(__DIR__ . '/_empty/_empty/'));
        $this->assertSame(true, $xr->enable());
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithDirNotExitst(): void
    {
        $xr = new Xr(dirForPath(__DIR__ . '/_not-found/'));
        $this->assertSame(true, $xr->enable());
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithSettingsFile(): void
    {
        $configDir = dirForPath(__DIR__ . '/_resources/');
        $return = include $configDir->path()->getChild('xr.php')->__toString();
        $xr = new Xr($configDir);
        $this->assertSame($return['enable'], $xr->enable());
        unset($return['enable']);
        $this->assertEquals(new Client(...$return), $xr->client());
    }
}
