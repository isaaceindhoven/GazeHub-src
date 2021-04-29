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

        $configRepo = $container->get(ConfigRepository::class);

        $logger = new Logger('GazeHub');

        if (count($options) === 0) {
            $logger->pushHandler(new StreamHandler('php://stdout', $configRepo->get('log_level')));
        }

        $container->set(LoggerInterface::class, $logger);
    }
}
