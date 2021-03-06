<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests;

use DI\Container;
use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Decoders\TokenDecoderJwtPlacebo;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\ClientRepositoryInMemory;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use ISAAC\GazeHub\Repositories\ConfigRepositoryFilesystem;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

abstract class BaseTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
        parent::__construct();
        $this->container = new Container();
        $this->fillContainer();
    }

    private function fillContainer(): void
    {
        $configRepo = new ConfigRepositoryFilesystem(__DIR__ . '/assets/gazehub.config.json');
        $tokenDecoderJwtPlacebo = new TokenDecoderJwtPlacebo();
        $logger = $this->createMock(LoggerInterface::class);
        $clientRepo = new ClientRepositoryInMemory($logger);
        $subRepo = new SubscriptionRepositoryInMemory($logger);

        $this->container->set(LoopInterface::class, Factory::create());
        $this->container->set(ConfigRepository::class, $configRepo);
        $this->container->set(TokenDecoder::class, $tokenDecoderJwtPlacebo);
        $this->container->set(ClientRepository::class, $clientRepo);
        $this->container->set(SubscriptionRepository::class, $subRepo);
        $this->container->set(LoggerInterface::class, $logger);
    }
}
