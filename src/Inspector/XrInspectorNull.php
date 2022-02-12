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

namespace Chevere\Xr\Inspector;

use Chevere\Xr\Interfaces\XrInspectorInterface;

final class XrInspectorNull implements XrInspectorInterface
{
    public function pause(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        return;
    }

    public function memory(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        return;
    }
}
