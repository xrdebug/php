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

use Chevere\Xr\Inspector\XrInspector;
use Chevere\Xr\Tests\_resources\XrClientTester;
use Chevere\Xr\Tests\_resources\XrClientTesterStop;
use Chevere\Xr\XrClient;
use PHPUnit\Framework\TestCase;

final class XrInspectorTest extends TestCase
{
    public function testConstruct(): void
    {
        $client = new XrClient();
        $this->expectNotToPerformAssertions();
        new XrInspector($client);
    }

    public function testMemory(): void
    {
        require_once __DIR__ . '/_resources/XrClientTester.php';
        $client = new XrClientTester();
        $inspector = new XrInspector($client);
        $topic = 'topic';
        $emote = 'emote';
        $flags = 0;
        $inspector->memory(t: $topic, e: $emote, f: $flags);
        $line = strval(__LINE__ - 1);
        $body = $client->getLastMessage()->toArray()['body'];
        $this->assertMatchesRegularExpression(
            '#[\d\.\,]+\s[\w]{2}#',
            $body,
        );
        $this->assertSame(
            [
                'body' => $body,
                'file_path' => __FILE__,
                'file_line' => $line,
                'emote' => $emote,
                'topic' => $topic,
                'id' => $client->getLastMessage()->toArray()['id'],
            ],
            $client->getLastMessage()->toArray()
        );
    }

    public function testPause(): void
    {
        require_once __DIR__ . '/_resources/XrClientTester.php';
        $client = new XrClientTester();
        $inspector = new XrInspector($client);
        $topic = 'topic';
        $emote = 'emote';
        $flags = 0;
        $inspector->pause(t: $topic, e: $emote, f: $flags);
        $line = strval(__LINE__ - 1);
        $this->assertSame(
            [
                'body' => '',
                'file_path' => __FILE__,
                'file_line' => $line,
                'emote' => $emote,
                'topic' => $topic,
                'id' => $client->getLastMessage()->toArray()['id'],
            ],
            $client->getLastMessage()->toArray()
        );
    }

    public function testPauseStop(): void
    {
        require_once __DIR__ . '/_resources/XrClientTesterStop.php';
        $client = new XrClientTesterStop();
        $inspector = new XrInspector($client);
        $this->expectOutputString("* stop\nexit 255");
        $inspector->pause();
    }
}
