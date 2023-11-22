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

use Chevere\Writer\Interfaces\WriterInterface;
use LogicException;
use function Chevere\Message\message;

/**
 * @codeCoverageIgnore
 */
final class WriterInstance
{
    private static WriterInterface $instance;

    public function __construct(WriterInterface $writer)
    {
        self::$instance = $writer;
    }

    public static function get(): WriterInterface
    {
        if (! isset(self::$instance)) {
            throw new LogicException(
                (string) message(
                    'No `%class%` instance present',
                    class: static::class
                )
            );
        }

        return self::$instance;
    }
}
