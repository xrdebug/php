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

namespace Chevere\Xr;

use Chevere\Xr\Interfaces\XrMessageInterface;

final class XrPause
{
    private string $key;

    public function __construct(private XrMessageInterface $message)
    {
        $this->key = md5(strval(time()));
    }

    public function message(): XrMessageInterface
    {
        return $this->message;
    }

    public function key(): string
    {
        return $this->key;
    }
}
