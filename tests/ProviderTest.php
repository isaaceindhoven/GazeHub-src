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

namespace ISAAC\GazeHub\Tests;

use ISAAC\GazeHub\Decoders\ITokenDecoder;
use ISAAC\GazeHub\Providers\ClientRepositoryProvider;
use ISAAC\GazeHub\Providers\ConfigRepositoryProvider;
use ISAAC\GazeHub\Providers\LoggerProvider;
use ISAAC\GazeHub\Providers\SubscriptionRepositoryProvider;
use ISAAC\GazeHub\Providers\TokenDecoderProvider;
use ISAAC\GazeHub\Repositories\IClientRepository;
use ISAAC\GazeHub\Repositories\IConfigRepository;
use ISAAC\GazeHub\Repositories\ISubscriptionRepository;
use Psr\Log\LoggerInterface;

use function PHPUnit\Framework\assertTrue;

class ProviderTest extends BaseTest
{
    public function testIfProvidersAllInjectedIntoTheContainer(): void
    {
        $providers = [
            ConfigRepositoryProvider::class => [IConfigRepository::class],
            LoggerProvider::class => [LoggerInterface::class],
            ClientRepositoryProvider::class => [IClientRepository::class],
            SubscriptionRepositoryProvider::class => [ISubscriptionRepository::class],
            TokenDecoderProvider::class => [ITokenDecoder::class],
        ];

        foreach ($providers as $provider => $classesToInject) {
            $provider = new $provider();
            $provider->register($this->container);

            foreach ($classesToInject as $classToInject) {
                assertTrue($this->container->has($classToInject));
            }
        }
    }
}
