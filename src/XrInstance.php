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

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Xr\Interfaces\XrInterface;

/**
 * @codeCoverageIgnore
 */
final class XrInstance
{
    private static XrInterface $instance;

    public function __construct(XrInterface $xr)
    {
        self::$instance = $xr;
    }

    public static function get(): XrInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException(
                message('No xr instance present')
            );
        }

        return self::$instance;
    }
}
