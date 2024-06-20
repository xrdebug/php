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

use Chevere\ThrowableHandler\Formats\HtmlFormat;
use Chevere\ThrowableHandler\Formats\PlainFormat;
use Chevere\ThrowableHandler\ThrowableRead;
use Chevere\Trace\Trace;
use Chevere\xrDebug\PHP\ThrowableParser;
use Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

final class ThrowableParserTest extends TestCase
{
    public function testTopLevel(): void
    {
        $throwable = new Exception('foo');
        $read = new ThrowableRead($throwable);
        $parser = new ThrowableParser($read);
        $this->assertSame(Exception::class, $parser->topic());
        $this->assertSame(
            Exception::class,
            $parser->throwableRead()->className()
        );
        $this->assertStringStartsWith(
            ThrowableParser::OPEN_TEMPLATE . "\n",
            $parser->body()
        );
        $this->assertStringEndsWith(
            ThrowableParser::CLOSE_TEMPLATE . "\n",
            $parser->body()
        );
        $this->assertStringContainsString(
            '<div class="throwable-code">0</div>',
            $parser->body()
        );
        $trace = new Trace(
            $parser->throwableRead()->trace(),
            new HtmlFormat()
        );
        $trace = (string) $trace;
        $this->assertStringContainsString(
            <<<HTML
            <div class="throwable-backtrace backtrace">{$trace}</div>
            HTML,
            $parser->body()
        );
        $this->assertSame('⚠️Throwable', $parser->emote());
        $this->assertStringContainsString(Exception::class, $parser->body());
    }

    public function testNamespaced(): void
    {
        $throwable = new TypeError('foo');
        $read = new ThrowableRead($throwable);
        $parser = new ThrowableParser($read);
        $this->assertSame('TypeError', $parser->topic());
        $this->assertSame(
            TypeError::class,
            $parser->throwableRead()->className()
        );
        $this->assertStringContainsString(
            '<div class="throwable-message">foo</div>',
            $parser->body()
        );
    }

    public function testWithPrevious(): void
    {
        $file = __FILE__;
        $line = __LINE__ + 1;
        $previous = new Exception('bar');
        $throwable = new Exception('foo', previous: $previous);
        $read = new ThrowableRead($throwable);
        $parser = new ThrowableParser($read);
        $body = strip_tags($parser->body());
        $this->assertStringContainsString(
            <<<PLAIN
            0 {$file}:{$line}
            {main}()
            PLAIN,
            $body
        );
    }

    public function testWithExtra(): void
    {
        $extra = 'EXTRA EXTRA! TODD SMELLS';
        $throwable = new Exception('foo');
        $read = new ThrowableRead($throwable);
        $format = new PlainFormat();
        $parser = new ThrowableParser($read, $extra);
        $this->assertStringContainsString($extra, $parser->body());
    }
}
