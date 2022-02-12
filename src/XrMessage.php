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

use Chevere\ThrowableHandler\Formats\ThrowableHandlerHtmlFormat;
use Chevere\Trace\TraceDocument;
use Chevere\VarDump\Formats\VarDumpHtmlFormat;
use Chevere\VarDump\VarDump;
use Chevere\Writer\Interfaces\WriterInterface;
use Chevere\Writer\NullWriter;
use Chevere\Xr\Interfaces\XrMessageInterface;
use Chevere\Xr\VarDump\Output\XrVarDumpHtmlOutput;

final class XrMessage implements XrMessageInterface
{
    private string $body = '';

    private string $topic = '';

    private string $emote = '';
    
    private string $filePath = '';

    private int $fileLine = 0;

    private bool $isFlagBacktrace = false;

    private array $vars = [];

    private WriterInterface $writer;

    private string $key;

    public function __construct(private array $backtrace = [])
    {
        if ($backtrace === []) {
            $this->backtrace = debug_backtrace();
        }
        $this->writer = new NullWriter();
        $this->filePath = strval($this->backtrace[0]['file'] ?? '');
        $this->fileLine = intval($this->backtrace[0]['line'] ?? 0);
        $this->key = md5(strval(time()));
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

    public function key(): string
    {
        return $this->key;
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

    public function withVars(...$vars): self
    {
        $new = clone $this;
        $new->vars = $vars;
        
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
            'file_path' => $this->filePath,
            'file_line' => strval($this->fileLine),
            'emote' => $this->emote,
            'topic' => $this->topic,
            'key' => $this->key,
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
            ->withVars(...$this->vars)
            ->process($this->writer);
        $dumpString = $this->writer->__toString();
        if ($dumpString !== '') {
            $this->body .= '<div class="dump">' . $dumpString . '</div>';
        }
    }

    private function handleBacktrace(): void
    {
        if ($this->isFlagBacktrace) {
            $traceDocument = new TraceDocument(
                $this->backtrace,
                new ThrowableHandlerHtmlFormat()
            );
            $this->body .= '<div class="backtrace">'
                . "\n"
                . $traceDocument->__toString()
                . '</div>';
        }
    }
}
