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

namespace Chevere\xrDebug\PHP\Traits;

use Chevere\xrDebug\PHP\Curl;
use Chevere\xrDebug\PHP\Exceptions\StopException;
use Chevere\xrDebug\PHP\Interfaces\CurlInterface;
use Chevere\xrDebug\PHP\Interfaces\MessageInterface;
use phpseclib3\Crypt\EC\PrivateKey;
use function Chevere\Message\message;
use function Chevere\xrDebug\PHP\sign;

trait ClientTrait
{
    private CurlInterface $curl;

    private string $scheme = 'http';

    /**
     * @var array <int, mixed>
     */
    private array $options = [];

    public function __construct(
        private string $host = 'localhost',
        private int $port = 27420,
        bool $isHttps = false,
        private ?PrivateKey $privateKey = null,
        ?CurlInterface $curl = null,
    ) {
        $this->curl = $curl ?? new Curl();
        if ($isHttps) {
            $this->scheme = 'https';
        }
    }

    public function curl(): CurlInterface
    {
        return $this->curl;
    }

    public function getUrl(string $endpoint): string
    {
        return "{$this->scheme}://{$this->host}:{$this->port}/{$endpoint}";
    }

    public function sendMessage(MessageInterface $message): void
    {
        try {
            $curl = $this->getCurlHandle(
                'POST',
                'messages',
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
                'POST',
                'pauses',
                $message->toArray(),
            );
            $curl->exec();
            $curlError = $curl->error();
            if ($curlError === '') {
                do {
                    sleep(1);
                } while ($this->isPaused($message->id()));
            }
        } finally {
            unset($curl);
        }
    }

    public function isPaused(string $id): bool
    {
        try {
            $curl = $this->getCurlHandle(
                'GET',
                'pauses/' . $id,
                []
            );
            $curlResult = $curl->exec();
            if (! $curlResult || $curl->error() !== '') {
                return false;
            }
            /** @var object $response */
            $response = json_decode(strval($curlResult));
            if ($response->stop ?? false) {
                throw new StopException(
                    (string) message(
                        '[STOP EXECUTION] triggered from %remote%',
                        remote: $this->host . ':' . $this->port
                    )
                );
            }

            return true;
        } finally {
            unset($curl);
        }
    }

    public function options(): array
    {
        return $this->options;
    }

    /**
     * @codeCoverageIgnore
     */
    public function exit(int $exitCode = 0): void
    {
        exit($exitCode);
    }

    /**
     *  @param array<string, string> $data
     */
    private function getCurlHandle(string $method, string $url, array $data): CurlInterface
    {
        $this->options = [
            CURLINFO_HEADER_OUT => true,
            CURLOPT_ENCODING => '',
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_URL => $this->getUrl($url),
            CURLOPT_USERAGENT => 'xrdebug/php',
        ];
        $this->handleSignature($data);
        $this->curl->setOptArray($this->options);

        return $this->curl;
    }

    /**
     * @param array<string, string> $data
     */
    private function handleSignature(array $data): void
    {
        if ($this->privateKey !== null) {
            $signatureDisplay = sign($this->privateKey, $data);
            $this->options[CURLOPT_HTTPHEADER] = [
                'X-Signature: ' . $signatureDisplay,
            ];
        }
    }
}
