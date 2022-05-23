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

use Chevere\Writer\Interfaces\WriterInterface;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;
use function Chevere\Xr\getWriter;
use Chevere\Xr\Message;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\Validator;

final class MessageTest extends TestCase
{
    private WriterInterface $writer;

    private function filterArray(array $array, string ...$filterKey): array
    {
        return array_intersect_key($array, array_flip($filterKey));
    }

    protected function setUp(): void
    {
        $this->writer = new StreamWriter(streamTemp(''));
    }

    public function testEmptyBacktrace(): void
    {
        $message = new Message();
        $line = __LINE__ - 1;
        $this->assertTrue((new Validator())->validate($message->id()));
        $this->assertSame(__FILE__, $message->filePath());
        $this->assertSame($line, $message->fileLine());
        $this->assertInstanceOf(WriterInterface::class, $message->writer());
        $this->assertSame(
            [
                'body' => '',
                'file_path' => __FILE__,
                'file_line' => strval($line),
                'emote' => '',
                'topic' => '',
                'id' => $message->id(),
            ],
            $message->toArray()
        );
    }

    public function testDeclaredBacktrace(): void
    {
        $testFile = 'test';
        $testLine = 1234;
        $trace = [
            [
                'file' => $testFile,
                'line' => $testLine,
            ],
        ];
        $message = new Message($trace);
        $this->assertSame($testFile, $message->filePath());
        $this->assertSame($testLine, $message->fileLine());
        $this->assertSame(
            [
                'file_path' => $testFile,
                'file_line' => strval($testLine),
            ],
            $this->filterArray($message->toArray(), 'file_path', 'file_line')
        );
    }

    public function testWithBody(): void
    {
        $message = new Message();
        $body = 'the body';
        $withBody = $message->withBody($body);
        $this->assertNotSame($message, $withBody);
        $this->assertSame($body, $withBody->body());
        $this->assertSame($body, $withBody->toArray()['body']);
    }

    public function testWithTopic(): void
    {
        $message = new Message();
        $topic = 'Topic';
        $withTopic = $message->withTopic($topic);
        $this->assertNotSame($message, $withTopic);
        $this->assertSame(
            $topic,
            $withTopic->topic()
        );
        $this->assertSame(
            $topic,
            $withTopic->toArray()['topic']
        );
    }

    public function testWithEmote(): void
    {
        $message = new Message();
        $emote = '😎';
        $withEmote = $message->withEmote($emote);
        $this->assertNotSame($message, $withEmote);
        $this->assertSame(
            $emote,
            $withEmote->emote()
        );
        $this->assertSame(
            $emote,
            $withEmote->toArray()['emote']
        );
    }

    public function testWithWriter(): void
    {
        $message = new Message();
        $writer = new StreamWriter(streamTemp('test'));
        $withWriter = $message->withWriter($writer);
        $this->assertNotSame($message, $withWriter);
        $this->assertSame(
            $writer,
            $withWriter->writer()
        );
    }

    public function testWithVars(): void
    {
        $message = (new Message())->withWriter(getWriter());
        $var = 'Hola, mundo!';
        $length = strlen($var);
        $withVars = $message->withVars($var);
        $this->assertNotSame($message, $withVars);
        $this->assertSame(
            $var,
            $withVars->vars()[0]
        );
        $this->assertSame('<div class="dump"><pre>
Arg•0 <span style="color:#ff8700">string</span> ' . $var . ' <em><span style="color:rgb(108 108 108 / 65%);">(length=' . $length . ')</span></em></pre></div>', $withVars->toArray()['body']);
    }

    public function testWithBacktraceFlag(): void
    {
        $message = new Message();
        $line = strval(__LINE__ - 1);
        $this->assertFalse($message->isEnableBacktrace());
        $withBacktraceFlag = $message->withFlags(XR_BACKTRACE);
        $this->assertNotSame($message, $withBacktraceFlag);
        $this->assertTrue($withBacktraceFlag->isEnableBacktrace());
        $this->assertStringContainsString(
            '<div class="backtrace">',
            $withBacktraceFlag->toArray()['body']
        );
        $this->assertStringContainsString(
            __FILE__ . ':' . $line,
            $withBacktraceFlag->toArray()['body']
        );
    }
}
