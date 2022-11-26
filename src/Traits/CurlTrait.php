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

namespace Chevere\Xr\Traits;

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\RuntimeException;
use CurlHandle;

trait CurlTrait
{
    private CurlHandle $handle;

    /**
     * @var string[]
     */
    private array $functions = [
        'curl_close',
        'curl_error',
        'curl_exec',
        'curl_init',
        'curl_setopt_array',
    ];

    /**
     * @codeCoverageIgnore
     */
    public function __construct(string $url = null)
    {
        $this->assertCurl();
        $this->handle = curl_init($url) ?:
            throw new RuntimeException(
                message('Unable to initialize cURL session')
            );
    }

    public function __destruct()
    {
        if (isset($this->handle)) {
            $this->close();
        }
    }

    public function handle(): CurlHandle
    {
        return $this->handle;
    }

    public function error(): string
    {
        return curl_error($this->handle);
    }

    public function exec(): string|bool
    {
        return curl_exec($this->handle);
    }

    public function setOptArray(array $options): bool
    {
        return curl_setopt_array($this->handle, $options);
    }

    public function close(): void
    {
        curl_close($this->handle);
    }

    private function assertCurl(): void
    {
        foreach ($this->functions as $function) {
            if (! function_exists($function)) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException(
                    message('Function %function% is not available')
                        ->withCode('%function%', $function)
                );
                // @codeCoverageIgnoreEnd
            }
        }
    }
}
