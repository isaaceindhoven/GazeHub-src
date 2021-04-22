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

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Models\Client;
use Psr\Log\LoggerInterface;

use function array_filter;
use function array_push;
use function count;

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
     * Find client by Token Id (jti claim in JWT)
     *
     * @param string        $tokenId        Token ID in JWT jti claim
     * @return Client|null
     */
    public function getByTokenId(string $tokenId): ?Client
    {
        foreach ($this->clients as $client) {
            if ($client->getTokenId() === $tokenId) {
                return $client;
            }
        }

        return null;
    }

    /**
     * Create and add a new client to this repository
     *
     * @param string[]      $roles      Client roles
     * @param string        $tokenId    Client token id
     * @return Client                   Newly created client
     */
    public function add(array $roles, string $tokenId): Client
    {
        $client = new Client($roles, $tokenId);

        array_push($this->clients, $client);
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
        $this->clients = array_filter(
            $this->clients,
            static function ($client) use ($clientToRemove): bool {
                return !$clientToRemove->equals($client);
            }
        );
        $this->logger->info('Client disconnected', ['connected clients' => count($this->clients)]);
    }
}
