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

use Chevere\Tests\src\ClientTester;
use Chevere\Tests\src\ClientTesterStop;
use Chevere\Xr\Client;
use Chevere\Xr\Inspector\Inspector;
use PHPUnit\Framework\TestCase;

final class InspectorTest extends TestCase
{
    public function testConstruct(): void
    {
        $client = new Client();
        $this->expectNotToPerformAssertions();
        new Inspector($client);
    }

    public function testMemory(): void
    {
        require_once __DIR__ . '/src/ClientTester.php';
        $client = new ClientTester();
        $inspector = new Inspector($client);
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
                'emote' => $emote,
                'file_line' => $line,
                'file_path' => __FILE__,
                'id' => $client->getLastMessage()->toArray()['id'],
                'topic' => $topic,
            ],
            $client->getLastMessage()->toArray()
        );
    }

    public function testPause(): void
    {
        require_once __DIR__ . '/src/ClientTester.php';
        $client = new ClientTester();
        $inspector = new Inspector($client);
        $topic = 'topic';
        $emote = 'emote';
        $flags = 0;
        $inspector->pause(t: $topic, e: $emote, f: $flags);
        $line = strval(__LINE__ - 1);
        $this->assertSame(
            [
                'body' => '',
                'emote' => $emote,
                'file_line' => $line,
                'file_path' => __FILE__,
                'id' => $client->getLastMessage()->toArray()['id'],
                'topic' => $topic,
            ],
            $client->getLastMessage()->toArray()
        );
    }

    public function testPauseStop(): void
    {
        require_once __DIR__ . '/src/ClientTesterStop.php';
        $client = new ClientTesterStop();
        $inspector = new Inspector($client);
        $this->expectOutputString("* stop\nexit 255");
        $inspector->pause();
    }
}
