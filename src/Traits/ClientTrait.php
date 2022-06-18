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
use Chevere\Xr\Curl;
use Chevere\Xr\Exceptions\StopException;
use Chevere\Xr\Interfaces\CurlInterface;
use Chevere\Xr\Interfaces\MessageInterface;

trait ClientTrait
{
    private CurlInterface $curl;

    public function __construct(
        private string $host = 'localhost',
        private int $port = 27420,
    ) {
        $this->curl = new Curl();
    }

    public function withCurl(CurlInterface $curl): self
    {
        $new = clone $this;
        $new->curl = $curl;

        return $new;
    }

    public function curl(): CurlInterface
    {
        return $this->curl;
    }

    public function getUrl(string $endpoint): string
    {
        return "http://{$this->host}:{$this->port}/{$endpoint}";
    }

    public function sendMessage(MessageInterface $message): void
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

    public function sendPause(MessageInterface $message): void
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

    public function isLocked(MessageInterface $message): bool
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
                throw new StopException(
                    message('[STOP EXECUTION] triggered from %remote%')
                        ->withStrtr('%remote%', $this->host . ':' . $this->port)
                );
            }

            return boolval($response->lock ?? false);
        } finally {
            unset($curl);
        }

        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function exit(int $exitCode = 0): void
    {
        exit($exitCode);
    }

    private function getCurlHandle(string $endpoint, array $data): CurlInterface
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
