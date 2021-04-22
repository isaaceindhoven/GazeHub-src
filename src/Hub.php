<?php

/**
  *   Do not remove or alter the notices in this preamble.
  *   This software code regards ISAAC Standard Software.
  *   Copyright © 2021 ISAAC and/or its affiliates.
  *   www.isaac.nl All rights reserved. License grant and user rights and obligations
  *   according to applicable license agreement. Please contact sales@isaac.nl for
  *   questions regarding license and user rights.
  */

declare(strict_types=1);

namespace ISAAC\GazeHub;

use DI\Container;
use Exception;
use ISAAC\GazeHub\Middlewares\CorsMiddleware;
use ISAAC\GazeHub\Middlewares\JsonParserMiddleware;
use ISAAC\GazeHub\Providers\Provider;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\Http\Server as HttpServer;
use React\Socket\Server;

use function get_class;
use function sprintf;

class Hub
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct()
    {
        $this->container = new Container();
        $this->loadProviders();
        $this->logger = $this->container->get(LoggerInterface::class);
    }

    public function run(): void
    {
        $config = $this->container->get(ConfigRepository::class);

        $host = $config->get('server_host');
        $port = $config->get('server_port');

        $loop = Factory::create();

        $socket = new Server(sprintf('%s:%s', $host, $port), $loop);

        $server = new HttpServer(
            $loop,
            [$this->container->get(CorsMiddleware::class), 'handle'],
            [$this->container->get(JsonParserMiddleware::class), 'handle'],
            [new Router($this->container), 'route']
        );

        $server->on('error', [$this, 'onError']);

        $server->listen($socket);

        $this->logger->info(sprintf('Server running on %s:%s', $host, $port));

        $loop->run();
    }

    public function onError(Exception $e): void
    {
        $this->logger->error($e->getMessage());
        if ($e->getPrevious() !== null && $e->getPrevious()->getMessage() !== '') {
            $this->logger->error($e->getPrevious()->getMessage());
        }
    }

    private function loadProviders(): void
    {
        $providers = require(__DIR__ . '/../config/providers.php');

        foreach ($providers as $provider) {
            $provider = new $provider();

            if (!($provider instanceof Provider)) {
                $className = get_class($provider);
                $this->logger->error(sprintf('Class %s is not an instance of Provider', $className));
                continue;
            }

            $provider->register($this->container);
        }
    }
}
