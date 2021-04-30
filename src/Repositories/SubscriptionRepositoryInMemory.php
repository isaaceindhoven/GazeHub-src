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
    private const ANY = 'GAZEHUB__ALL';

    /**
     * [
     *      "ProductCreated" => [
     *          "GAZEHUB__ALL" => [CLIENTS],
     *          "admin" => [CLIENTS]
     *      ]
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
        $this->makeKeyOnArrayIfNotExist($this->subscriptions[$topic], self::ANY);

        $this->subscriptions[$topic][self::ANY][] = $client;

        foreach ($client->getRoles() as $role) {
            $this->makeKeyOnArrayIfNotExist($this->subscriptions[$topic], $role);
            $this->subscriptions[$topic][$role][] = $client;
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

        $this->logger->info('Unsubscribe client from topic', [$topic]);

        foreach ($this->subscriptions[$topic] as $role => $clients) {
            $this->subscriptions[$topic][$role] = array_values(array_filter(
                $clients,
                static function ($client) use ($clientToUnsubscribe): bool {
                    return !$clientToUnsubscribe->equals($client);
                }
            ));
        }

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
            return $this->subscriptions[$topic][self::ANY];
        }

        if (array_key_exists($role, $this->subscriptions[$topic])) {
            return $this->subscriptions[$topic][$role];
        }

        return [];
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
