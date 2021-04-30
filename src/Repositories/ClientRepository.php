<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Models\Client;

interface ClientRepository
{
    /**
     * Find client by unique token ID
     *
     * @param string $tokenId
     * @return Client|null
     */
    public function getByTokenId(string $tokenId): ?Client;

    /**
     * Add a new client to the repository
     *
     * @param string[] $roles
     * @param string $tokenId
     * @return Client
     */
    public function add(array $roles, string $tokenId): Client;

    /**
     * Remove client from repository
     *
     * @param Client $client
     */
    public function remove(Client $client): void;
}
