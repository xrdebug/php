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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;

/**
 * @codeCoverageIgnore
 */
final class XrInstance
{
    private static Xr $instance;

    public function __construct(Xr $xr)
    {
        self::$instance = $xr;
    }

    public static function get(): Xr
    {
        if (!isset(self::$instance)) {
            throw new LogicException(
                new Message('No xr instance present')
            );
        }

        return self::$instance;
    }
}
