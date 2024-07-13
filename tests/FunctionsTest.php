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

use Chevere\Writer\StreamWriter;
use Chevere\xrDebug\PHP\WriterInstance;
use PHPUnit\Framework\TestCase;
use function Chevere\Writer\streamTemp;
use function Chevere\xrDebug\PHP\getWriter;

final class FunctionsTest extends TestCase
{
    public function testXr(): void
    {
        $previousWriter = getWriter();
        $writer = new StreamWriter(streamTemp(''));
        new WriterInstance($writer);
        $var = 'Hola xr!';
        $length = strlen($var);
        xr($var, t: 'Topic', e: '😎', f: XR_BACKTRACE);
        $this->assertSame(
            <<<HTML
            <pre>
            1: <span class="chv-dump-string">string</span> {$var} <em><span class="chv-dump-emphasis">(length={$length})</span></em></pre>
            HTML,
            $writer->__toString()
        );
        new WriterInstance($previousWriter);
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
