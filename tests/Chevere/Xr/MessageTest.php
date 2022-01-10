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

use function Chevere\Components\Writer\streamFor;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Interfaces\Writer\WriterInterface;
use Chevere\Xr\Components\Xr\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    private WriterInterface $writer;

    private function getMessage(
        array $vars = [],
        int $shift = 1,
        string $topic = '',
        string $emote = '',
        int $flags = 0,
    ): Message {
        $message = new Message($this->writer, $vars, $shift);
        if ($topic !== '') {
            $message = $message->withTopic($topic);
        }
        if ($emote !== '') {
            $message = $message->withEmote($emote);
        }
        if ($flags !== 0) {
            $message = $message->withFlags($flags);
        }

        return $message;
    }

    private function filterArray(array $array, string ...$filterKey): array
    {
        return array_intersect_key($array, array_flip($filterKey));
    }

    protected function setUp(): void
    {
        $this->writer = new StreamWriter(streamFor('php://temp', 'r+'));
    }

    public function testEmpty(): void
    {
        $message = $this->getMessage();
        $this->assertSame(
            [
                'body' => '',
                'emote' => '',
                'topic' => '',
                'pause' => '0',
            ],
            $this->filterArray($message->data(), 'body', 'emote', 'topic', 'pause')
        );
    }

    public function testCaller(): void
    {
        $message = $this->getMessage();
        $line = __LINE__ - 1;
        $this->assertSame(
            [
                'file_path' => __FILE__,
                'file_line' => (string) $line,
            ],
            $this->filterArray($message->data(), 'file_path', 'file_line')
        );
    }

    public function testBacktraceArray(): void
    {
        $message = $this->getMessage();
        $line = __LINE__ - 1;
        $this->assertSame(
            [
                'file' => __FILE__,
                'line' => (int) $line,
                'function' => 'getMessage',
                'class' => __CLASS__
            ],
            $this->filterArray($message->backtrace()[0], 'file', 'line', 'function', 'class')
        );
    }

    public function testWithTopic(): void
    {
        $this->assertSame('Topic', $this->getMessage(topic: 'Topic')->data()['topic']);
    }

    public function testWithEmote(): void
    {
        $this->assertSame('😎', $this->getMessage(emote: '😎')->data()['emote']);
    }

    public function testDump(): void
    {
        $var = 'Hola, mundo!';
        $message = $this->getMessage(vars: [$var]);
        $this->assertSame('<div class="dump"><pre>
Arg:0 <span style="color:#ff8700">string</span> ' . $var . ' <em><span style="color:rgb(108 108 108 / 65%);">(length=12)</span></em></pre></div>', $message->data()['body']);
    }

    public function testWithBacktraceFlag(): void
    {
        $message = $this->getMessage(flags: XR_BACKTRACE);
        $line = (string) (__LINE__ - 1);
        $this->assertStringContainsString('<div class="backtrace">', $message->data()['body']);
        $this->assertStringContainsString(__FILE__ . ':' . $line, $message->data()['body']);
    }

    public function testWithPauseFlag(): void
    {
        $message = $this->getMessage(flags: XR_PAUSE);
        $this->assertSame('1', $message->data()['pause']);
    }
}
