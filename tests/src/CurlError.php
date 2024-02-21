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

namespace Chevere\Tests\src;

use Chevere\xrDebug\PHP\Interfaces\CurlInterface;
use Chevere\xrDebug\PHP\Traits\CurlTrait;

final class CurlError implements CurlInterface
{
    use CurlTrait;

    public function error(): string
    {
        return 'oops';
    }

    public function exec(): string|bool
    {
        return '';
    }
}
