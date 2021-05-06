<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Repositories;

use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Repositories\ClientRepositoryInMemory;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function count;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function uniqid;

class ClientRepositoryInMemoryTest extends TestCase
{
    public function testShouldCreateAndStoreClient(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();

        // Act
        $client = $clientRepo->add();

        // Assert
        assertNotNull($client);
        assertNotEmpty($client->getId());
    }

    public function testShouldReturnClientBasedOnTokenId(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();
        $client1 = $this->addClientToRepo($clientRepo);
        $this->addClientToRepo($clientRepo);

        // Act
        $foundClient = $clientRepo->getById($client1->getId());

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
        assertNull($clientRepo->getById($client1->getId()));
        assertEquals($client2, $clientRepo->getById($client2->getId()));
    }

    public function testShouldGetAllAdminSubscriptions(): void
    {
        // Arrange
        $clientRepo = $this->createClientRepo();
        $client1 = $clientRepo->add();
        $client2 = $clientRepo->add();
        $client3 = $clientRepo->add();

        $client1->setRoles(['admin']);

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
        $client = $repository->add();
        $client->setRoles(['admin', 'client']);
        return $client;
    }

    private function createClientRepo(): ClientRepositoryInMemory
    {
        $logger = $this->createMock(LoggerInterface::class);
        return new ClientRepositoryInMemory($logger);
    }
}
