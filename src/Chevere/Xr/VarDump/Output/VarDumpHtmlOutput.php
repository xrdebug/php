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

namespace Chevere\Xr\VarDump\Output;

use Chevere\VarDump\Interfaces\VarDumpFormatInterface;
use Chevere\VarDump\Outputs\VarDumpAbstractOutput;

final class VarDumpHtmlOutput extends VarDumpAbstractOutput
{
    public function tearDown(): void
    {
        $this->writer()->write('</pre>');
    }

    public function prepare(): void
    {
        $this->caller = '';
        $this->writer()->write('<pre>');
    }

    public function writeCallerFile(VarDumpFormatInterface $formatter): void
    {
        return;
    }
}
