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

namespace GazeHub\Tests\Services;

use GazeHub\Models\Client;
use GazeHub\Services\ClientRepository;
use PHPUnit\Framework\TestCase;

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

    private function addClientToRepo(ClientRepository $repository): Client
    {
        return $repository->add(['admin', 'client'], uniqid());
    }
}
