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

/**
 * Describes the component in charge of defining the client.
 */
interface ClientInterface
{
    public function getUrl(string $endpoint): string;

    public function sendMessage(MessageInterface $message): void;

    public function sendPause(MessageInterface $message): void;

    public function exit(int $exitCode = 0): void;
}
