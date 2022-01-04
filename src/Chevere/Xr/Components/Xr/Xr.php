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

namespace Chevere\Xr\Components\Xr;

use function Chevere\Components\Filesystem\filePhpReturnForPath;
use function Chevere\Components\Type\typeArray;
use Chevere\Interfaces\Filesystem\DirInterface;
use Throwable;

final class Xr
{
    private bool $enable = true;

    private Client $client;

    private string $configFile;

    private array $configNames = ['xr.php'];

    private array $defaultSettings = [
        'enable' => true,
        'host' => 'localhost',
        'port' => 27420,
    ];

    private array $settings;

    public function __construct(private DirInterface $configDir)
    {
        $this->settings = $this->defaultSettings;
        $args = [];
        $this->configFile = $this->getConfigFile();
        if ($this->configFile !== '') {
            try {
                $return = filePhpReturnForPath($this->configFile)
                    ->varType(typeArray());
                $this->settings = array_merge($this->settings, $return);
                $args = [
                    'host' => $this->settings['host'] ?? $this->defaultSettings['host'],
                    'port' => $this->settings['port'] ?? $this->defaultSettings['port'],
                ];
            }
            // @codeCoverageIgnoreStart
            catch (Throwable) {
            }
            // @codeCoverageIgnoreEnd
        }
        $this->client = new Client(...$args);
    }

    public function enable(): bool
    {
        return $this->settings['enable'] ?? false;
    }

    public function client(): Client
    {
        return $this->client;
    }

    private function getConfigFile(): string
    {
        $configDirectory = $this->configDir->path()->toString();
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
}
