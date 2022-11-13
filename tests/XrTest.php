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

use function Chevere\Filesystem\directoryForPath;
use Chevere\Xr\Client;
use Chevere\Xr\Xr;
use phpseclib3\Crypt\EC;
use PHPUnit\Framework\TestCase;

final class XrTest extends TestCase
{
    public function testConstructDefault(): void
    {
        $xr = new Xr();
        $args = [
            'isEnabled' => true,
            'isHttps' => false,
            'host' => 'localhost',
            'port' => 27420,
        ];
        foreach ($args as $prop => $value) {
            $this->assertSame($value, $xr->{$prop}());
        }
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithArguments(): void
    {
        $key = EC::createKey('Ed25519');
        $args = [
            'isEnabled' => false,
            'isHttps' => false,
            'host' => 'test',
            'port' => 1234,
            'key' => $key->toString('PKCS8'),
        ];
        $xr = new Xr(...$args);
        foreach ($args as $prop => $value) {
            $this->assertSame($value, $xr->{$prop}());
        }
    }

    public function testConstructWithoutSettingsFileSubfolder(): void
    {
        $xr = (new Xr())
            ->withConfigDir(directoryForPath(__DIR__ . '/_empty/_empty/'));
        $this->assertSame(true, $xr->isEnabled());
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithDirNotExists(): void
    {
        $xr = (new Xr())
            ->withConfigDir(directoryForPath(__DIR__ . '/_not-found/'));
        $this->assertSame(true, $xr->isEnabled());
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithSettingsFile(): void
    {
        $configDir = directoryForPath(__DIR__ . '/_resources/');
        $return = include $configDir->path()->getChild('xr.php')->__toString();
        $xr = (new Xr())->withConfigDir($configDir);
        $this->assertSame($return['isEnabled'], $xr->isEnabled());
        unset($return['isEnabled'], $return['key']);
        $this->assertEquals(new Client(...$return), $xr->client());
    }
}
