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

use Chevere\xrDebug\PHP\Client;
use Chevere\xrDebug\PHP\Message;
use Chevere\xrDebug\PHP\Xr;
use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\PublicKeyLoader;
use PHPUnit\Framework\TestCase;
use function Chevere\Filesystem\directoryForPath;

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
        $xr = new Xr();
        $with = $xr->withConfigDir(
            directoryForPath(__DIR__ . '/_empty/_empty/')
        );
        $this->assertNotSame($xr, $with);
        $this->assertSame(true, $with->isEnabled());
        $this->assertEquals(new Client(), $with->client());
    }

    public function testConstructWithDirNotExists(): void
    {
        $xr = (new Xr())->withConfigDir(
            directoryForPath(__DIR__ . '/_not-found/')
        );
        $this->assertSame(true, $xr->isEnabled());
        $this->assertEquals(new Client(), $xr->client());
    }

    public function testConstructWithSettingsFile(): void
    {
        $configDir = directoryForPath(__DIR__ . '/src/');
        $return = include $configDir->path()->getChild('xr.php')->__toString();
        $xr = (new Xr())->withConfigDir($configDir);
        $this->assertSame($return['isEnabled'], $xr->isEnabled());
        $return['privateKey'] = PublicKeyLoader::load($return['key']);
        unset($return['isEnabled'], $return['key']);
        $client = new Client(...$return);
        $this->assertEquals($client->getUrl('test'), $xr->client()->getUrl('test'));
        $message = new Message([
            [
                'path' => 'test',
                'line' => '123',
            ],
        ]);
        $client->sendMessage($message);
        $xr->client()->sendMessage($message);
        $this->assertSame($client->options(), $xr->client()->options());
    }
}
