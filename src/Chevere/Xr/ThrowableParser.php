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

use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerHtmlFormatter;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use Chevere\Components\ThrowableHandler\ThrowableTraceFormatter;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerFormatterInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableReadInterface;
use Throwable;

class ThrowableParser
{
    private string $topic = '';

    private string $body = '';

    private string $emote = '⚠️Throwable';

    private ThrowableHandlerFormatterInterface $format;

    private int $index = 0;

    public const OPEN_TEMPLATE = '<div class="throwable">';

    public const CLOSE_TEMPLATE = '</div>';

    public const ITEM_TEMPLATE = <<<HTML
        <div class="throwable-item">
            <h2 class="throwable-title">%title%</h2>
            <div class="throwable-code">%code%</div>
            <div class="throwable-message">%message%</div>
            %extra%
            <div class="throwable-backtrace backtrace">%trace%</div>
        </div>
    HTML;

    public function __construct(
        private Throwable $throwable,
        private string $extra = '',
    ) {
        $this->throwableRead = new ThrowableRead($throwable);
        $this->format = new ThrowableHandlerHtmlFormatter();
        $this->topic = basename(
            str_replace(
                '\\',
                DIRECTORY_SEPARATOR,
                $this->throwableRead->className()
            )
        );
        $this->appendBodyLine(static::OPEN_TEMPLATE);
        do {
            $this->index++;
        } while ($throwable = $this->parse($throwable));
        $this->appendBodyLine(static::CLOSE_TEMPLATE);
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

    public function throwableRead(): ThrowableReadInterface
    {
        return $this->throwableRead;
    }

    private function appendBodyLine(string $body): void
    {
        $this->body .= $body . "\n";
    }

    private function parse(Throwable $throwable): ?Throwable
    {
        if ($this->index === 1) {
            $read = $this->throwableRead;
            $trace = $this->throwableRead->trace();
        } else {
            $read = new ThrowableRead($throwable);
            $trace = [
                [
                    'function' => '{main}',
                    'file' => $read->file(),
                    'line' => $read->line(),
                ]
            ];
        }
        $traceFormatter = new ThrowableTraceFormatter(
            $trace,
            $this->format
        );
        $translate = [
                '%title%' => $read->className(),
                '%code%' => $read->code(),
                '%message%' => $read->message(),
                '%extra%' => $this->index === 1
                    ? $this->extra
                    : '',
                '%trace%' => $traceFormatter->__toString(),
            ];
        $this->appendBodyLine(strtr(static::ITEM_TEMPLATE, $translate));

        return $throwable->getPrevious();
    }
}
