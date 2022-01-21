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

use function Chevere\Components\Writer\streamTemp;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Xr\WriterInstance;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testXr(): void
    {
        $writer = new StreamWriter(streamTemp(''));
        new WriterInstance($writer);
        xr('Hola, mundo!', t: 'Topic', e: '😎', f: XR_BACKTRACE | XR_PAUSE);
        $this->assertSame(
            '<pre>
Arg:0 <span style="color:#ff8700">string</span> Hola, mundo! <em><span style="color:rgb(108 108 108 / 65%);">(length=12)</span></em></pre>',
            $writer->__toString()
        );
    }
}
