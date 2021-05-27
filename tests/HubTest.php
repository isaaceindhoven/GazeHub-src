<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests;

use ISAAC\GazeHub\Hub;
use ISAAC\GazeHub\Tests\Providers\InvalidProvider;
use ISAAC\GazeHub\Tests\Providers\ValidProvider;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

use function React\Promise\Timer\resolve;

class HubTest extends BaseTest
{
    public function testHubCanBeInstantiatedWithoutExceptions(): void
    {
        $hub = new Hub([], $this->container);
        $loop = $this->container->get(LoopInterface::class);
        resolve(0, $loop)->then(static function () use ($loop): void {
            $loop->stop();
        });
        $hub->run();

        self::assertIsObject($hub);
    }

    public function testHubWillLogErrorWhenProviderClassNotFound(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $this->container->set(LoggerInterface::class, $logger);

        $hub = new Hub([
            '',
        ], $this->container);
    }

    public function testHubWillLogErrorWhenProviderNotInstanceOfProviderInterface(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $this->container->set(LoggerInterface::class, $logger);

        $hub = new Hub([InvalidProvider::class], $this->container);
    }

    public function testHubWillCallProviderRegisterMethod(): void
    {
        $hub = new Hub([ValidProvider::class], $this->container);

        self::assertEquals('registered', $this->container->get('ValidProviderTest'));
    }

    public function testOnErrorMethodWillLogMessage(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $this->container->set(LoggerInterface::class, $logger);

        $hub = new Hub([], $this->container);
        $hub->onError(new \Exception('Test'));
    }
}
