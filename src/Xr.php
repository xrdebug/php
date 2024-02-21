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

namespace Chevere\xrDebug\PHP;

use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\xrDebug\PHP\Interfaces\ClientInterface;
use Chevere\xrDebug\PHP\Interfaces\CurlInterface;
use Chevere\xrDebug\PHP\Interfaces\XrInterface;
use phpseclib3\Crypt\EC\PrivateKey;
use phpseclib3\Crypt\PublicKeyLoader;
use Throwable;
use function Chevere\Filesystem\filePhpReturnForPath;

final class Xr implements XrInterface
{
    private ClientInterface $client;

    private DirectoryInterface $directory;

    private string $configFile = '';

    /**
     * @var array<string>
     */
    private array $configNames = ['xr.php'];

    private CurlInterface $curl;

    private ?PrivateKey $privateKey = null;

    public function __construct(
        private bool $isEnabled = true,
        private bool $isHttps = false,
        private string $host = 'localhost',
        private int $port = 27420,
        private string $key = '',
    ) {
        $this->curl = new Curl();
        $this->setClient();
    }

    public function withConfigDir(DirectoryInterface $config): XrInterface
    {
        $new = clone $this;
        $new->directory = $config;

        try {
            $new->configFile = $new->getConfigFile();
        }
        // @codeCoverageIgnoreStart
        catch (Throwable) {
            // Ignore directory not found
        }
        // @codeCoverageIgnoreEnd
        if ($new->configFile !== '') {
            $new->setConfigFromFile();
        }
        $new->setClient();

        return $new;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function isHttps(): bool
    {
        return $this->isHttps;
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function key(): string
    {
        return $this->key;
    }

    private function setConfigFromFile(): void
    {
        try {
            /** @var array<string, string|int|bool> $return */
            $return = filePhpReturnForPath($this->configFile)->cast()->array();
            foreach (static::CONFIG_NAMES as $prop) {
                $this->{$prop} = $return[$prop] ?? $this->{$prop};
            }
        }
        // @codeCoverageIgnoreStart
        catch (Throwable) {
            // Ignore to use defaults
        }
        // @codeCoverageIgnoreEnd
    }

    private function getConfigFile(): string
    {
        $configDirectory = $this->directory->path()->__toString();
        while (is_dir($configDirectory)) {
            foreach ($this->configNames as $configName) {
                $configFullPath = $configDirectory . $configName;
                if (file_exists($configFullPath)) {
                    return $configFullPath;
                }
            }
            // @infection-ignore-all
            $parentDirectory = dirname($configDirectory) . DIRECTORY_SEPARATOR;
            // @infection-ignore-all
            if ($parentDirectory === $configDirectory) {
                return '';
            }
            $configDirectory = $parentDirectory;
        }

        return ''; // @codeCoverageIgnore
    }

    private function setClient(): void
    {
        if ($this->key !== '') {
            /** @var ?PrivateKey $loadKey */
            $loadKey = PublicKeyLoader::load($this->key);
            $this->privateKey = $loadKey;
        }
        $this->client = (
            new Client(
                host: $this->host,
                port: $this->port,
                isHttps: $this->isHttps,
                privateKey: $this->privateKey,
            )
        )->withCurl($this->curl);
    }
}
