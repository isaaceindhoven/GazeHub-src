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
use ISAAC\GazeHub\Services\SubscriptionRepository;
use PHPUnit\Framework\TestCase;
use React\Stream\ThroughStream;

use function count;
use function PHPUnit\Framework\assertEquals;
use function uniqid;

class SubscriptionRepositoryTest extends TestCase
{
    public function testShouldAddSubscription(): void
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient();
        $subscriptionRequest = ['topics' => ['ABC'], 'callbackId' => uniqid()];

        // Act
        $subscriptionRepository->add($client1, $subscriptionRequest['topics'], $subscriptionRequest['callbackId']);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopicAndRole('ABC');
        assertEquals(1, count($subs));
        assertEquals($subs[0]->client->tokenId, $client1->tokenId);
        assertEquals($subs[0]->callbackId, $subscriptionRequest['callbackId']);
    }

    public function testShouldRemoveAllClientSubscriptions(): void
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient();
        $subscriptionRequest1 = ['topics' => ['ABC'], 'callbackId' => uniqid()];
        $client2 = $this->createClient();
        $subscriptionRequest2 = ['topics' => ['ABC'], 'callbackId' => uniqid()];

        // Act
        $subscriptionRepository->add($client1, $subscriptionRequest1['topics'], $subscriptionRequest1['callbackId']);
        $subscriptionRepository->add($client2, $subscriptionRequest2['topics'], $subscriptionRequest2['callbackId']);
        $subscriptionRepository->remove($client1);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopicAndRole('ABC');
        assertEquals(1, count($subs));
        assertEquals($subs[1]->client->tokenId, $client2->tokenId);
        assertEquals($subs[1]->callbackId, $subscriptionRequest2['callbackId']);
    }

    public function testShouldRemoveSingleSubscriptions(): void
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient();
        $subscriptionRequest = ['topics' => ['1', '2'], 'callbackId' => uniqid()];

        // Act
        $subscriptionRepository->add($client1, $subscriptionRequest['topics'], $subscriptionRequest['callbackId']);
        $subscriptionRepository->remove($client1, ['2']);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopicAndRole('1');
        assertEquals(1, count($subs));

        assertEquals($subs[0]->client->tokenId, $client1->tokenId);
        assertEquals($subs[0]->callbackId, $subscriptionRequest['callbackId']);
    }

    public function testShouldGetAllAdminSubscriptions(): void
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient(['admin']);
        $client2 = $this->createClient(['normal']);
        $client3 = $this->createClient(['admin', 'normal']);
        $subscriptionRequest1 = ['topics' => ['1', '2'], 'callbackId' => uniqid()];
        $subscriptionRequest2 = ['topics' => ['1', '2'], 'callbackId' => uniqid()];
        $subscriptionRequest3 = ['topics' => ['1', '2'], 'callbackId' => uniqid()];

        $subscriptionRepository->add($client1, $subscriptionRequest1['topics'], $subscriptionRequest1['callbackId']);
        $subscriptionRepository->add($client2, $subscriptionRequest2['topics'], $subscriptionRequest2['callbackId']);
        $subscriptionRepository->add($client3, $subscriptionRequest3['topics'], $subscriptionRequest3['callbackId']);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopicAndRole('1', 'admin');
        assertEquals(2, count($subs));
    }

    /**
     * @param string[] $roles
     * @return Client
     */
    private function createClient(array $roles = []): Client
    {
        $client = new Client();
        $client->stream = new ThroughStream();
        $client->roles = $roles;
        $client->tokenId = uniqid();
        return $client;
    }
}
