<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use ISAAC\GazeHub\Repositories\ConfigRepositoryFilesystem;

class ConfigRepositoryProvider implements Provider
{
    public function register(Container &$container): void
    {
        $container->set(
            ConfigRepository::class,
            new ConfigRepositoryFilesystem(__DIR__ . '/../../config/config.php')
        );
    }
}
