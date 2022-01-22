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

use function Chevere\Components\Message\message;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Xr\ThrowableParser;
use Exception;
use PHPUnit\Framework\TestCase;

final class ThrowableParserTest extends TestCase
{
    public function testTopLevel(): void
    {
        $exception = new Exception('foo');
        $parser = new ThrowableParser($exception, '');
        $this->assertSame(Exception::class, $parser->topic());
        $this->assertSame(
            Exception::class,
            $parser->throwableRead()->className()
        );
        $this->assertSame('⚠️Throwable', $parser->emote());
        $this->assertStringContainsString(Exception::class, $parser->body());
    }
    
    public function testNamespaced(): void
    {
        $exception = new TypeException(message: message('foo'));
        $parser = new ThrowableParser($exception, '');
        $this->assertSame('TypeException', $parser->topic());
        $this->assertSame(
            TypeException::class,
            $parser->throwableRead()->className()
        );
        $this->assertStringContainsString(ThrowableParser::class, $parser->body());
    }

    public function testWithPrevious(): void
    {
        $exception = new Exception('foo', previous: new Exception('bar'));
        $parser = new ThrowableParser($exception, '');
        $this->assertStringContainsString(ThrowableParser::class, $parser->body());
    }

    public function testWithExtra(): void
    {
        $extra = 'EXTRA EXTRA! TODD SMELLS';
        $exception = new Exception('foo');
        $parser = new ThrowableParser($exception, $extra);
        $this->assertStringContainsString($extra, $parser->body());
    }
}
