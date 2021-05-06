<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Models\Client;
use Psr\Log\LoggerInterface;

use function array_key_exists;
use function count;
use function uniqid;

class ClientRepositoryInMemory implements ClientRepository
{
    /**
     * @var Client[]
     */
    private $clients = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Find client by Id
     *
     * @param string        $id
     * @return Client|null
     */
    public function getById(string $id): ?Client
    {
        return array_key_exists($id, $this->clients) ? $this->clients[$id] : null;
    }

    /**
     * Create and add a new client to this repository
     *
     * @return Client                   Newly created client
     */
    public function add(): Client
    {
        $client = new Client([], uniqid('', true));

        $this->clients[$client->getId()] = $client;
        $this->logger->info('Client connected', ['connected clients' => count($this->clients)]);

        return $client;
    }

    /**
     * Remove client from repository, the stream is not closed automatically.
     *
     * @param Client        $clientToRemove
     */
    public function remove(Client $clientToRemove): void
    {
        unset($this->clients[$clientToRemove->getId()]);

        $this->logger->info('Client disconnected', ['connected clients' => count($this->clients)]);
    }
}
