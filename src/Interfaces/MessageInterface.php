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

use Chevere\Common\Interfaces\ToArrayInterface;
use Chevere\Writer\Interfaces\WriterInterface;

/**
 * Describes the component in charge of defining the XR message.
 */
interface MessageInterface extends ToArrayInterface
{
    public function body(): string;

    public function topic(): string;

    public function emote(): string;

    public function filePath(): string;

    public function fileLine(): int;

    public function isEnableBacktrace(): bool;

    public function vars(): array;

    public function id(): string;

    public function writer(): WriterInterface;

    public function withBody(string $body): self;

    public function withTopic(string $topic): self;

    public function withEmote(string $emote): self;

    public function withWriter(WriterInterface $writer): self;

    public function withVars(...$vars): self;

    public function withFlags(int $flags): self;

    public function toArray(): array;
}
