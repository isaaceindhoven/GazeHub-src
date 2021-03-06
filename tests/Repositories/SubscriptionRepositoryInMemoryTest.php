<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Repositories;

use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function count;
use function PHPUnit\Framework\assertEquals;
use function uniqid;

// phpcs:ignore ObjectCalisthenics.Metrics.MethodPerClassLimit.ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff
class SubscriptionRepositoryInMemoryTest extends TestCase
{
    public function testIfSubscribeWorksWithClientWithNoRoles(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client([], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated');

        assertEquals(1, count($clients));
        assertEquals($client1->getId(), $clients[0]->getId());
    }

    public function testIfGetterWorksWithEmptyRoleString(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client([], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', '');

        assertEquals(1, count($clients));
        assertEquals($client1->getId(), $clients[0]->getId());
    }

    public function testIfSubscribeWorksWithClientWithRoleAdmin(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $client2 = new Client([], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client2, 'ProductCreated');
        $adminClients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');
        $allClients = $subRepo->getClientsByTopicAndRole('ProductCreated');

        assertEquals(1, count($adminClients));
        assertEquals(2, count($allClients));
        assertEquals($client1->getId(), $adminClients[0]->getId());
        assertEquals($client1->getId(), $allClients[0]->getId());
        assertEquals($client2->getId(), $allClients[1]->getId());
    }

    public function testIfClientCanOnlyBeSubscribedOnceToTopic(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client1, 'ProductCreated');
        $adminClients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($adminClients));
        assertEquals($client1->getId(), $adminClients[0]->getId());
    }

    public function testIfClientCanSubscribeToMultipleTopics(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client1, 'ProductUpdated');
        $adminClients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($adminClients));
        assertEquals($client1->getId(), $adminClients[0]->getId());
        assertEquals(['ProductCreated', 'ProductUpdated'], $adminClients[0]->getTopics());
    }

    public function testIfUnsubscribeWorksForSingleTopic(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client1, 'ProductUpdated');
        $subRepo->unsubscribe($client1, 'ProductUpdated');
        $adminClients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($adminClients));
        assertEquals($client1->getId(), $adminClients[0]->getId());
        assertEquals(['ProductCreated'], $adminClients[0]->getTopics());
    }

    public function testIfRightClientIsRemovedOnUnsubscribe(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $client2 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client2, 'ProductCreated');
        $subRepo->unsubscribe($client1, 'ProductCreated');
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($clients));
        assertEquals($client2->getId(), $clients[0]->getId());
        assertEquals(['ProductCreated'], $clients[0]->getTopics());
    }

    public function testIfUnsubscribeAlsoMatchesWithTopic(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->unsubscribe($client1, 'HALLO');
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($clients));
        assertEquals($client1->getId(), $clients[0]->getId());
        assertEquals(['ProductCreated'], $clients[0]->getTopics());
    }

    public function testIfUnsubscribeWithEmptyTopicString(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->unsubscribe($client1, '');
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($clients));
        assertEquals($client1->getId(), $clients[0]->getId());
        assertEquals(['ProductCreated'], $clients[0]->getTopics());
    }

    public function testIfUnsubscribeWorksOnNonSubscribedClient(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $subRepo->unsubscribe($client1, 'ProductCreated');
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(0, count($clients));
    }

    public function testIfRemoveRemovesTheRightClient(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $client2 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client2, 'ProductCreated');
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client1, 'ProductUpdated');
        $subRepo->remove($client1);
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($clients));
        assertEquals($client2->getId(), $clients[0]->getId());
        assertEquals(0, count($client1->getTopics()));
    }

    public function testIfRemoveWorksIfClientNotSubscribed(): void
    {
        $subRepo = $this->createSubRepo();

        $client1 = new Client(['admin'], uniqid());
        $client2 = new Client(['admin'], uniqid());
        $subRepo->subscribe($client1, 'ProductCreated');
        $subRepo->subscribe($client1, 'ProductUpdated');
        $subRepo->remove($client2);
        $clients = $subRepo->getClientsByTopicAndRole('ProductCreated', 'admin');

        assertEquals(1, count($clients));
        assertEquals($client1->getId(), $clients[0]->getId());
    }

    private function createSubRepo(): SubscriptionRepositoryInMemory
    {
        $logger = $this->createMock(LoggerInterface::class);
        return new SubscriptionRepositoryInMemory($logger);
    }
}
