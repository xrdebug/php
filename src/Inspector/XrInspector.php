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

use Chevere\Xr\Inspector\Traits\XrInspectorTrait;
use Chevere\Xr\Interfaces\XrInspectorInterface;

class XrInspector implements XrInspectorInterface
{
    use XrInspectorTrait;

    public function memory(
        string $e = '',
        string $t = '',
        int $f = 0,
    ): void {
        $memory = memory_get_usage(true);
        $this->sendMessage(
            body: sprintf('%.2F MB', $memory / 1000000),
            topic: $t,
            emote: $e,
            flags: $f,
        );
    }
}
