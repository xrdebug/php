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

/**
 * Describes the component in charge of defining the client which send messages
 * to the xrDebug server.
 */
interface ClientInterface
{
    public function sendMessage(MessageInterface $message): void;

    public function sendPause(MessageInterface $message): void;

    public function isPaused(string $id): bool;

    public function curl(): CurlInterface;

    public function getUrl(string $endpoint): string;

    /**
     * @return array<int, mixed>
     */
    public function options(): array;

    public function exit(int $exitCode = 0): void;
}
