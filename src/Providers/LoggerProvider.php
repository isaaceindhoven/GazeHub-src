<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\IConfigRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerProvider implements IProvider
{
    public function register(Container &$container): void
    {
        $logger = new Logger('GazeHub');
        $configRepo = $container->get(IConfigRepository::class);
        $logger->pushHandler(new StreamHandler('php://stdout', $configRepo->get('log_level')));
        $container->set(LoggerInterface::class, $logger);
    }
}
