<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Models\Client;

interface SubscriptionRepository
{
    /**
     * @param Client $client
     * @param string $topic
     * @return void
     */
    public function subscribe(Client $client, string $topic): void;

    /**
     * @param Client $clientToUnsubscribe
     * @param string $topic
     * @return void
     */
    public function unsubscribe(Client $clientToUnsubscribe, string $topic): void;

    /**
     * @param Client $clientToUnsubscribe
     * @return void
     */
    public function remove(Client $clientToUnsubscribe): void;

    /**
     * @param string $topic
     * @param string $role
     * @return Client[]
     */
    public function getClientsByTopicAndRole(string $topic, string $role = null): array;
}
