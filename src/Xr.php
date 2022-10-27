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

use function Chevere\Filesystem\filePhpReturnForPath;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use function Chevere\Type\typeArray;
use Chevere\Xr\Interfaces\ClientInterface;
use Chevere\Xr\Interfaces\CurlInterface;
use Chevere\Xr\Interfaces\XrInterface;
use Throwable;

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

    public function __construct(
        private bool $isEnabled = true,
        private bool $isHttps = false,
        private string $host = 'localhost',
        private int $port = 27420
    ) {
        $this->curl = new Curl();
        $this->setClient();
    }

    public function withConfigDir(DirectoryInterface $config): XrInterface
    {
        $new = clone $this;
        $new->directory = $config;
        $new->configFile = $new->getConfigFile();
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

    private function setConfigFromFile(): void
    {
        try {
            /** @var array<string, string|int|bool> $return */
            $return = filePhpReturnForPath($this->configFile)
                ->variableTyped(typeArray());
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
            $parentDirectory = dirname($configDirectory) . DIRECTORY_SEPARATOR;
            if ($parentDirectory === $configDirectory) {
                return '';
            }
            $configDirectory = $parentDirectory;
        }

        return ''; // @codeCoverageIgnore
    }

    private function setClient(): void
    {
        $this->client = (new Client(
            host: $this->host,
            port: $this->port,
            isHttps: $this->isHttps,
        ))->withCurl($this->curl);
    }
}
