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

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Xr\Exceptions\XrStopException;
use Chevere\Xr\Interfaces\XrClientInterface;
use Chevere\Xr\Interfaces\XrMessageInterface;
use CurlHandle;

final class XrClient implements XrClientInterface
{
    public function __construct(
        private string $host = 'localhost',
        private int $port = 27420,
    ) {
    }

    public function getUrl(string $endpoint): string
    {
        return "http://{$this->host}:{$this->port}/{$endpoint}";
    }

    public function sendMessage(XrMessageInterface $message): void
    {
        try {
            $curlHandle = $this->getCurlHandle(
                'message',
                $message->toArray()
            );
            curl_exec($curlHandle);
        } finally {
            curl_close($curlHandle);
        }
    }

    public function sendPause(XrPause $pause): void
    {
        try {
            $curlHandle = $this->getCurlHandle(
                'lock-post',
                $pause->message()->toArray()
            );
            curl_exec($curlHandle);
            $curlError = curl_error($curlHandle);
            if ($curlError === '') {
                do {
                    sleep(1);
                } while ($this->isLocked($pause));
            }
        } finally {
            curl_close($curlHandle);
        }
    }

    public function isLocked(XrPause $pause): bool
    {
        try {
            $curlHandle = $this->getCurlHandle(
                'locks',
                ['key' => $pause->key()]
            );
            $curlError = null;
            $curlResult = curl_exec($curlHandle);
            $curlError = curl_error($curlHandle);
            if ($curlError !== '') {
                throw new LogicException();
            }
            if (!$curlResult) {
                return false;
            }
            $response = json_decode($curlResult);
            if ($response->stop ?? false) {
                throw new XrStopException(
                    message('[STOP EXECUTION] triggered from %remote%')
                        ->strtr('%remote%', $this->host . ':' . $this->port)
                );
            }

            return boolval($response->active ?? false);
        } finally {
            curl_close($curlHandle);
        }

        return false;
    }

    private function getCurlHandle(string $endpoint, array $data): CurlHandle
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
        curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 2);
        curl_setopt($curlHandle, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'chevere/xr 1.0');

        return $curlHandle;
    }
}
