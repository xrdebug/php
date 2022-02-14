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
use Chevere\Xr\Exceptions\XrStopException;
use Chevere\Xr\Interfaces\XrClientInterface;
use Chevere\Xr\Interfaces\XrCurlInterface;
use Chevere\Xr\Interfaces\XrMessageInterface;

final class XrClient implements XrClientInterface
{
    private XrCurlInterface $curl;

    public function __construct(
        private string $host = 'localhost',
        private int $port = 27420,
    ) {
        $this->curl = new XrCurl();
    }

    public function withCurl(XrCurlInterface $curl): self
    {
        $new = clone $this;
        $new->curl = $curl;

        return $new;
    }

    public function curl(): XrCurlInterface
    {
        return $this->curl;
    }

    public function getUrl(string $endpoint): string
    {
        return "http://{$this->host}:{$this->port}/{$endpoint}";
    }

    public function sendMessage(XrMessageInterface $message): void
    {
        try {
            $curl = $this->getCurlHandle(
                'message',
                $message->toArray()
            );
            $curl->exec();
        } finally {
            unset($curl);
        }
    }

    public function sendPause(XrMessageInterface $message): void
    {
        try {
            $curl = $this->getCurlHandle(
                'lock-post',
                $message->toArray()
            );
            $curl->exec();
            $curlError = $curl->error();
            if ($curlError === '') {
                do {
                    sleep(1);
                } while ($this->isLocked($message));
            }
        } finally {
            unset($curl);
        }
    }

    public function isLocked(XrMessageInterface $message): bool
    {
        try {
            $curl = $this->getCurlHandle(
                'locks',
                ['id' => $message->id()]
            );
            $curlResult = $curl->exec();
            if (!$curlResult || $curl->error() !== '') {
                return false;
            }
            $response = json_decode($curlResult);
            if ($response->stop ?? false) {
                throw new XrStopException(
                    message('[STOP EXECUTION] triggered from %remote%')
                        ->strtr('%remote%', $this->host . ':' . $this->port)
                );
            }

            return boolval($response->lock ?? false);
        } finally {
            unset($curl);
        }

        return false;
    }

    private function getCurlHandle(string $endpoint, array $data): XrCurlInterface
    {
        $this->curl->setOptArray(
            [
                CURLINFO_HEADER_OUT => true,
                CURLOPT_ENCODING => '',
                CURLOPT_FAILONERROR => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_URL => $this->getUrl($endpoint),
                CURLOPT_USERAGENT => 'chevere/xr 1.0',
            ]
        );

        return $this->curl;
    }
}
