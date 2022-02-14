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

use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;
use function Chevere\Xr\getWriter;
use Chevere\Xr\XrWriterInstance;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testXr(): void
    {
        $previousWriter = getWriter();
        $writer = new StreamWriter(streamTemp(''));
        new XrWriterInstance($writer);
        $var = 'Hola xr!';
        $length = strlen($var);
        xr($var, t: 'Topic', e: '😎', f: XR_BACKTRACE);
        $this->assertSame(
            '<pre>
Arg:0 <span style="color:#ff8700">string</span> ' . $var . ' <em><span style="color:rgb(108 108 108 / 65%);">(length=' . $length . ')</span></em></pre>',
            $writer->__toString()
        );
        new XrWriterInstance($previousWriter);
    }

    public function testXrr(): void
    {
        $this->expectNotToPerformAssertions();
        xrr('Hola xrr!');
    }

    public function testXri(): void
    {
        $this->expectNotToPerformAssertions();
        xri()->memory();
        xri()->pause();
    }
}
