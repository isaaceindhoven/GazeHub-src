<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

use function count;
use function getopt;

class LoggerProvider implements Provider
{
    public function register(Container &$container): void
    {
        $options = getopt('q');

        $logLevel = 'INFO';

        if ($container->has(ConfigRepository::class)) {
            $configRepo = $container->get(ConfigRepository::class);
            $logLevel = $configRepo->get('log_level');
        }

        $logger = new Logger('GazeHub');

        if (count($options) === 0) {
            $logger->pushHandler(new StreamHandler('php://stdout', $logLevel));
        }

        $container->set(LoggerInterface::class, $logger);
    }
}
