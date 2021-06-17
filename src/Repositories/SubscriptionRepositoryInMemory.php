<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Models\Client;
use Psr\Log\LoggerInterface;

use function array_filter;
use function array_key_exists;
use function array_values;
use function trim;

class SubscriptionRepositoryInMemory implements SubscriptionRepository
{
    /**
     * [
     *    "ProductCreated" => [CLIENT]
     * ]
     *
     * @var mixed[]
     */
    private $subscriptions = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Client $client
     * @param string $topic
     * @return void
     */
    public function subscribe(Client $client, string $topic): void
    {
        $this->logger->info('Subscribing client to topic', [$topic]);

        if ($client->hasTopic($topic)) {
            return;
        }

        $client->addTopics([$topic]);

        $this->makeKeyOnArrayIfNotExist($this->subscriptions, $topic);

        $this->subscriptions[$topic][] = $client;
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

        $this->logger->info('Unsubscribe client from topic', [$topic]);

        $this->subscriptions[$topic] = array_values(array_filter(
            $this->subscriptions[$topic],
            static function ($client) use ($clientToUnsubscribe): bool {
                return !$clientToUnsubscribe->equals($client);
            }
        ));

        $clientToUnsubscribe->removeTopics([$topic]);
    }

    /**
     * @param Client $clientToUnsubscribe
     * @return void
     */
    public function remove(Client $clientToUnsubscribe): void
    {
        foreach ($clientToUnsubscribe->getTopics() as $topic) {
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
            return $this->subscriptions[$topic];
        }

        return array_filter($this->subscriptions[$topic], static function (Client $client) use ($role): bool {
            return $client->hasRole($role);
        });
    }

    /**
     * @param mixed[] $arr
     * @param string $key
     * @return void
     */
    private function makeKeyOnArrayIfNotExist(array &$arr, string $key): void
    {
        if (!array_key_exists($key, $arr)) {
            $arr[$key] = [];
        }
    }
}
