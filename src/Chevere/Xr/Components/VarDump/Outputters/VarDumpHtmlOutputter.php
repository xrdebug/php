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

namespace Chevere\Xr\Components\VarDump\Outputters;

use Chevere\Components\VarDump\Outputters\VarDumpAbstractOutputter;
use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;

final class VarDumpHtmlOutputter extends VarDumpAbstractOutputter
{
    public const STYLE = '';

    public function tearDown(): void
    {
        $this->writer()->write('</pre>');
    }

    public function prepare(): void
    {
        $this->writer()->write(
            implode('', [
                '<pre style="' . self::STYLE . '">',
            ])
        );
    }

    public function writeCallerFile(VarDumpFormatterInterface $formatter): void
    {
        return;
    }
}
