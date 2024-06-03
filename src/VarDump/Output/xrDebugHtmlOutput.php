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

namespace Chevere\xrDebug\PHP\VarDump\Output;

use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Outputs\Output;

final class xrDebugHtmlOutput extends Output
{
    public function finalize(): void
    {
        $this->writer()->write('</pre>');
    }

    public function prepare(): void
    {
        $this->writer()->write('<pre>');
    }

    public function writeCallerFile(FormatInterface $format): void
    {
        // null override
    }
}
