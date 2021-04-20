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

namespace ISAAC\GazeHub\Tests\Repositories;

use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Repositories\ClientRepositoryInMemory;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function count;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function uniqid;

class ClientRepositoryInMemoryTest extends TestCase
{
    public function testShouldCreateAndStoreClient(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();
        $tokenPayload = ['roles' => ['admin', 'client'], 'jti' => 'randomId'];

        // Act
        $client = $clientRepo->add($tokenPayload['roles'], $tokenPayload['jti']);

        // Assert
        assertEquals($tokenPayload['roles'], $client->getRoles());
        assertEquals($tokenPayload['jti'], $client->getTokenId());
    }

    public function testShouldReturnClientBasedOnTokenId(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();
        $client1 = $this->addClientToRepo($clientRepo);
        $this->addClientToRepo($clientRepo);

        // Act
        $foundClient = $clientRepo->getByTokenId($client1->getTokenId());

        // Assert
        assertNotNull($foundClient);
        assertEquals($client1, $foundClient);
    }

    public function testShouldRemoveClientFromRepo(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();
        $client1 = $this->addClientToRepo($clientRepo);
        $client2 = $this->addClientToRepo($clientRepo);

        // Act
        $clientRepo->remove($client1);

        // Assert
        assertNull($clientRepo->getByTokenId($client1->getTokenId()));
        assertEquals($client2, $clientRepo->getByTokenId($client2->getTokenId()));
    }

    public function testShouldGetAllAdminSubscriptions(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();
        $client1 = $clientRepo->add(['admin'], 'Client1');
        $client2 = $clientRepo->add([], 'Client2');
        $client3 = $clientRepo->add([], 'Client3');

        $subRepo = new SubscriptionRepositoryInMemory($this->createMock(LoggerInterface::class));
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client2, 'ProductCreated');
        $subRepo->subscribe($client3, 'ProductCreated');

        // Assert
        $adminClients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');
        $normalClients = $subRepo->getClientsByTopicAndRole('ProductCreated', '');
        assertEquals(1, count($adminClients));
        assertEquals(3, count($normalClients));
    }

    private function addClientToRepo(ClientRepositoryInMemory $repository): Client
    {
        return $repository->add(['admin', 'client'], uniqid());
    }

    private function createClientRepo(): ClientRepositoryInMemory
    {
        $logger = $this->createMock(LoggerInterface::class);
        return new ClientRepositoryInMemory($logger);
    }
}
