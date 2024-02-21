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

namespace Chevere\xrDebug\PHP\Interfaces;

use Chevere\Filesystem\Interfaces\DirectoryInterface;

/**
 * Describes the component in charge of defining XR.
 */
interface XrInterface
{
    public const CONFIG_NAMES = ['isEnabled', 'isHttps', 'host', 'port', 'key'];

    public function withConfigDir(DirectoryInterface $config): self;

    public function isEnabled(): bool;

    public function isHttps(): bool;

    public function host(): string;

    public function port(): int;

    public function key(): string;

    public function client(): ClientInterface;
}
