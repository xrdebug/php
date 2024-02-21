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
use Chevere\xrDebug\PHP\Client;
use Chevere\xrDebug\PHP\Inspector\Inspector;
use Chevere\xrDebug\PHP\Message;
use PHPUnit\Framework\TestCase;

final class InspectorTest extends TestCase
{
    public function testConstruct(): void
    {
        $client = new Client();
        $this->expectNotToPerformAssertions();
        new Inspector($client);
    }

    public function commandProvider(): array
    {
        return [
            // [
            //     'pause',
            //     'pause',
            //     [
            //         't' => 'topic',
            //         'e' => 'emote',
            //         'f' => 0,
            //     ],
            // ],
            [
                'memory',
                'message',
                [
                    't' => 't',
                    'e' => 'e',
                    'f' => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider commandProvider
     */
    public function testCommand(string $method, string $handler, array $args): void
    {
        $expected = (new Message())
            ->withTopic($args['t'])
            ->withEmote($args['e'])
            ->withFlags($args['f']);

        $client = $this->createMock(Client::class);
        $client
            ->expects($this->once())
            ->method('send' . ucfirst($handler))
            ->with(
                $this->callback(
                    function ($message) use ($expected) {
                        return $message->topic() === $expected->topic()
                            && $message->emote() === $expected->emote()
                            && $message->isEnableBacktrace() === $expected->isEnableBacktrace();
                    }
                )
            );

        $inspector = new Inspector($client);
        $inspector->{$method}(...$args);
    }

    public function testPause(): void
    {
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
        $client = new ClientTesterStop();
        $inspector = new Inspector($client);
        $this->expectOutputString("* stop\nexit 255");
        $inspector->pause();
    }

    public function testMemory(): void
    {
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
}
