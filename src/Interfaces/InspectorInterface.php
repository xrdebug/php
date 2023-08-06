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

namespace Chevere\Xr\Interfaces;

/**
 * Describes the component in charge of defining the XR inspector default interface.
 */
interface InspectorInterface
{
    public function pause(
        string $body = '',
        string $t = '',
        string $e = '',
        int $f = 0,
    ): void;

    public function memory(
        string $t = '',
        string $e = '',
        int $f = 0,
    ): void;
}
