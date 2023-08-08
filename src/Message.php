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

use Chevere\ThrowableHandler\Formats\HtmlFormat as ThrowableHandlerHtmlFormat;
use Chevere\Trace\Trace;
use Chevere\VarDump\Formats\HtmlFormat as VarDumpHtmlFormat;
use Chevere\VarDump\VarDump;
use Chevere\Writer\Interfaces\WriterInterface;
use Chevere\Writer\NullWriter;
use Chevere\Xr\Interfaces\MessageInterface;
use Chevere\Xr\VarDump\Output\XrVarDumpHtmlOutput;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Uuid;

final class Message implements MessageInterface
{
    private string $body = '';

    private string $topic = '';

    private string $emote = '';

    private string $filePath = '';

    private int $fileLine = 0;

    private bool $isFlagBacktrace = false;

    /**
     * @var array<int|string, mixed>
     */
    private array $vars = [];

    private WriterInterface $writer;

    private string $id;

    /**
     * @param array<int, array<string, mixed>> $backtrace
     */
    public function __construct(
        private array $backtrace = []
    ) {
        if ($backtrace === []) {
            $this->backtrace = debug_backtrace();
        }
        $this->writer = new NullWriter();
        /** @var string $file */
        $file = $this->backtrace[0]['file'] ?? '';
        /** @var int $line */
        $line = $this->backtrace[0]['line'] ?? 0;
        $this->filePath = strval($file);
        $this->fileLine = intval($line);
        $node = (new RandomNodeProvider())->getNode();
        $this->id = Uuid::uuid1($node)->__toString();
    }

    public function body(): string
    {
        return $this->body;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function emote(): string
    {
        return $this->emote;
    }

    public function filePath(): string
    {
        return $this->filePath;
    }

    public function fileLine(): int
    {
        return $this->fileLine;
    }

    public function isEnableBacktrace(): bool
    {
        return $this->isFlagBacktrace;
    }

    public function vars(): array
    {
        return $this->vars;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function writer(): WriterInterface
    {
        return $this->writer;
    }

    public function withBody(string $body): self
    {
        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    public function withTopic(string $topic): self
    {
        $new = clone $this;
        $new->topic = $topic;

        return $new;
    }

    public function withEmote(string $emote): self
    {
        $new = clone $this;
        $new->emote = $emote;

        return $new;
    }

    public function withWriter(WriterInterface $writer): self
    {
        $new = clone $this;
        $new->writer = $writer;

        return $new;
    }

    public function withVariables(mixed ...$variables): self
    {
        $new = clone $this;
        $new->vars = $variables;

        return $new;
    }

    public function withFlags(int $flags): self
    {
        $new = clone $this;
        if ($flags & XR_BACKTRACE) {
            $new->isFlagBacktrace = true;
        }

        return $new;
    }

    public function toArray(): array
    {
        $this->handleDumpVars();
        $this->handleBacktrace();

        return [
            'body' => $this->body,
            'emote' => $this->emote,
            'file_line' => strval($this->fileLine),
            'file_path' => $this->filePath,
            'id' => $this->id,
            'topic' => $this->topic,
        ];
    }

    private function handleDumpVars(): void
    {
        if ($this->vars === []) {
            return;
        }
        (new VarDump(
            new VarDumpHtmlFormat(),
            new XrVarDumpHtmlOutput()
        ))
            ->withVariables(...$this->vars)
            ->process($this->writer);
        $dump = $this->writer->__toString();
        if ($dump === '') {
            return;
        }
        $this->body .= <<<HTML
        <div class="dump">{$dump}</div>
        HTML;
    }

    private function handleBacktrace(): void
    {
        if (! $this->isFlagBacktrace) {
            return;
        }
        $trace = new Trace(
            $this->backtrace,
            new ThrowableHandlerHtmlFormat()
        );
        $this->body .= <<<HTML
        <div class="backtrace">{$trace->__toString()}</div>
        HTML;
    }
}
