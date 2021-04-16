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

namespace ISAAC\GazeHub\Tests\Services;

use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Services\ClientRepository;
use PHPUnit\Framework\TestCase;

use function count;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function uniqid;

class ClientRepositoryTest extends TestCase
{
    public function testShouldCreateAndStoreClient(): void
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $tokenPayload = ['roles' => ['admin', 'client'], 'jti' => 'randomId'];

        // Act
        $client = $clientRepo->add($tokenPayload['roles'], $tokenPayload['jti']);

        // Assert
        assertEquals($tokenPayload['roles'], $client->roles);
        assertEquals($tokenPayload['jti'], $client->tokenId);
    }

    public function testShouldReturnClientBasedOnTokenId(): void
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client1 = $this->addClientToRepo($clientRepo);
        $this->addClientToRepo($clientRepo);

        // Act
        $foundClient = $clientRepo->getByTokenId($client1->tokenId);

        // Assert
        assertNotNull($foundClient);
        assertEquals($client1, $foundClient);
    }

    public function testShouldRemoveClientFromRepo(): void
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client1 = $this->addClientToRepo($clientRepo);
        $client2 = $this->addClientToRepo($clientRepo);

        // Act
        $clientRepo->remove($client1);

        // Assert
        assertNull($clientRepo->getByTokenId($client1->tokenId));
        assertEquals($client2, $clientRepo->getByTokenId($client2->tokenId));
    }

    public function testShouldGetAllAdminSubscriptions(): void
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client1 = $clientRepo->add(['admin'], 'Client1');
        $client2 = $clientRepo->add([], 'Client2');
        $client3 = $clientRepo->add([], 'Client3');

        $client1->topics = ['ProductCreated'];
        $client2->topics = ['ProductCreated'];
        $client3->topics = ['ProductCreated'];

        // Assert
        $adminClients = $clientRepo->getClientsByTopicAndRole('ProductCreated', 'admin');
        $normalClients = $clientRepo->getClientsByTopicAndRole('ProductCreated', '');
        assertEquals(1, count($adminClients));
        assertEquals(3, count($normalClients));
    }

    private function addClientToRepo(ClientRepository $repository): Client
    {
        return $repository->add(['admin', 'client'], uniqid());
    }
}
