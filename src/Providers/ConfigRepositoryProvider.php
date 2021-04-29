<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Exceptions\ConfigFileNotValidException;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use ISAAC\GazeHub\Repositories\ConfigRepositoryFilesystem;

use function count;
use function getcwd;
use function getopt;
use function is_string;

class ConfigRepositoryProvider implements Provider
{
    /**
     * @param Container $container
     * @throws ConfigFileNotValidException
     */
    public function register(Container &$container): void
    {
        $configPath = getcwd() . '/gazehub.config.json';

        $options = getopt('c::');

        if (count($options) > 0 && is_string($options['c'])) {
            $configPath = $options['c'];
        }

        $container->set(
            ConfigRepository::class,
            new ConfigRepositoryFilesystem($configPath)
        );
    }
}
