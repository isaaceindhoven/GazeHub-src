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

namespace ISAAC\GazeHub\Services;

use ISAAC\GazeHub\Models\Client;

use function array_filter;
use function array_key_exists;
use function trim;

class SubscriptionRepository
{
    private const ANY = 'GAZEHUB__ALL';

    /**
     * @var mixed[]
     */
    private $subscriptions = [];

    /**
     * @param Client $client
     * @param string $topic
     * @return void
     */
    public function subscribe(Client $client, string $topic): void
    {
        if (!array_key_exists($topic, $this->subscriptions)) {
            $this->subscriptions[$topic] = [];
        }

        $topicSubscription = &$this->subscriptions[$topic];

        $client->addTopics([$topic]);

        if (!array_key_exists(self::ANY, $topicSubscription)) {
            $topicSubscription[self::ANY] = [];
        }

        $topicSubscription[self::ANY][] = $client;

        foreach ($client->roles as $role) {
            if (!array_key_exists($role, $topicSubscription)) {
                $topicSubscription[$role] = [];
            }

            $topicSubscription[$role][] = $client;
        }
    }

    /**
     * @param Client $clientToUnsubscribe
     * @param string $topic
     * @return void
     */
    public function unsubscribe(Client $clientToUnsubscribe, string $topic): void
    {
        if (!array_key_exists($topic, $this->subscriptions)) {
            return;
        }

        foreach ($this->subscriptions[$topic] as $role => $clients) {
            $this->subscriptions[$topic][$role] = array_filter(
                $clients,
                static function ($client) use ($clientToUnsubscribe): bool {
                    return $client->tokenId !== $clientToUnsubscribe->tokenId;
                }
            );
        }

        $clientToUnsubscribe->removeTopics([$topic]);
    }

    /**
     * @param Client $clientToUnsubscribe
     * @return void
     */
    public function remove(Client $clientToUnsubscribe): void
    {
        foreach ($clientToUnsubscribe->topics as $topic) {
            $this->unsubscribe($clientToUnsubscribe, $topic);
        }
    }

    /**
     * @param string $topic
     * @param string $role
     * @return Client[]
     */
    public function getClientsByTopicAndRole(string $topic, string $role = null): array
    {
        if ($role !== null && trim($role) === '') {
            $role = null;
        }

        if (!array_key_exists($topic, $this->subscriptions)) {
            return [];
        }

        if ($role === null) {
            return $this->subscriptions[$topic][self::ANY];
        }

        if (array_key_exists($role, $this->subscriptions[$topic])) {
            return $this->subscriptions[$topic][$role];
        }

        return [];
    }
}
