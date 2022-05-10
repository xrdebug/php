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

namespace Chevere\Xr\Tests\_resources;

use Chevere\Xr\Interfaces\CurlInterface;
use Chevere\Xr\Traits\XrCurlTrait;

final class XrCurlStopTrue implements CurlInterface
{
    use XrCurlTrait;

    public function error(): string
    {
        return '';
    }

    public function exec(): string|bool
    {
        return '{"stop":true}';
    }
}
