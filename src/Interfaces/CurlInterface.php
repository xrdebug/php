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

use CurlHandle;

/**
 * Describes the component in charge of defining a curl abstraction.
 */
interface CurlInterface
{
    public function __destruct();

    public function handle(): ?CurlHandle;

    public function error(): string;

    public function exec(): string|bool;

    /**
     * @param array<int, mixed> $options
     */
    public function setOptArray(array $options): bool;

    public function close(): void;
}
