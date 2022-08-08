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

use Chevere\Filesystem\Interfaces\DirectoryInterface;

/**
 * Describes the component in charge of defining XR.
 */
interface XrInterface
{
    public function withConfigDir(DirectoryInterface $config): XrInterface;

    public function enable(): bool;

    public function client(): ClientInterface;

    public function host(): string;

    public function port(): int;
}
