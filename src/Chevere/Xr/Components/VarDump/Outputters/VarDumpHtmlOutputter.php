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
    public const BACKGROUND = '#132537';

    public const BACKGROUND_SHADE = '#132537';

    /**
     * @var string Dump style, no double quotes.
     */
    public const STYLE = "font: 14px 'Fira Code Retina', 'Operator Mono', Inconsolata, Consolas,
    monospace, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: " . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';

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
