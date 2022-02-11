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
use Chevere\Xr\Xr;
use Chevere\Xr\XrClient;
use PHPUnit\Framework\TestCase;

final class XrTest extends TestCase
{
    public function testConstructDefault(): void
    {
        $xr = new Xr();
        $args = [
            'enable' => true,
            'host' => 'localhost',
            'port' => 27420,
        ];
        foreach ($args as $prop => $value) {
            $this->assertSame($value, $xr->{$prop}());
        }
        $this->assertEquals(new XrClient(), $xr->client());
    }

    public function testConstructWithArguments(): void
    {
        $args = [
            'enable' => false,
            'host' => 'test',
            'port' => 1234,
        ];
        $xr = new Xr(...$args);
        foreach ($args as $prop => $value) {
            $this->assertSame($value, $xr->{$prop}());
        }
        $this->assertEquals(new XrClient($args['host'], $args['port']), $xr->client());
    }

    public function testConstructWithoutSettingsFileSubfolder(): void
    {
        $xr = (new Xr())
            ->withConfigDir(dirForPath(__DIR__ . '/_empty/_empty/'));
        $this->assertSame(true, $xr->enable());
        $this->assertEquals(new XrClient(), $xr->client());
    }

    public function testConstructWithDirNotExitst(): void
    {
        $xr = (new Xr())
            ->withConfigDir(dirForPath(__DIR__ . '/_not-found/'));
        $this->assertSame(true, $xr->enable());
        $this->assertEquals(new XrClient(), $xr->client());
    }

    public function testConstructWithSettingsFile(): void
    {
        $configDir = dirForPath(__DIR__ . '/_resources/');
        $return = include $configDir->path()->getChild('xr.php')->__toString();
        $xr = (new Xr())->withConfigDir($configDir);
        $this->assertSame($return['enable'], $xr->enable());
        unset($return['enable']);
        $this->assertEquals(new XrClient(...$return), $xr->client());
    }
}
