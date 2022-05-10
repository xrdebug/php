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

use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Xr\Interfaces\InspectorInterface;

/**
 * @codeCoverageIgnore
 */
final class InspectorInstance
{
    private static InspectorInterface $instance;

    public function __construct(InspectorInterface $xrInspector)
    {
        self::$instance = $xrInspector;
    }

    public static function get(): InspectorInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException(
                new Message('No xr inspector instance present')
            );
        }

        return self::$instance;
    }
}
