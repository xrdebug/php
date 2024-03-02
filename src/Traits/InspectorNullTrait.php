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

namespace Chevere\xrDebug\PHP\Traits;

/**
 * @infection-ignore-all
 */
trait InspectorNullTrait
{
    public function pause(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        // null
    }

    public function memory(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        // null
    }
}
