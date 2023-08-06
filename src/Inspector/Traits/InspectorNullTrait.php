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

namespace Chevere\Xr\Inspector\Traits;

trait InspectorNullTrait
{
    public function pause(
        string $body = '',
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        // dummy
    }

    public function memory(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        // dummy
    }
}
