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

use Chevere\Writer\Interfaces\WriterInterface;

/**
 * Describes the component in charge of defining the XR message.
 */
interface MessageInterface
{
    public function body(): string;

    public function topic(): string;

    public function emote(): string;

    public function filePath(): string;

    public function fileLine(): int;

    public function isEnableBacktrace(): bool;

    /**
     * @return array<int|string, mixed>
     */
    public function vars(): array;

    public function id(): string;

    public function writer(): WriterInterface;

    public function withBody(string $body): static;

    public function withTopic(string $topic): static;

    public function withEmote(string $emote): static;

    public function withWriter(WriterInterface $writer): static;

    public function withVariables(mixed ...$variables): static;

    public function withFlags(int $flags): static;

    public function withPath(string $path): static;

    /**
     * @return array<string, string>
     */
    public function toArray(): array;
}
