<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Models\Client;

interface ClientRepository
{
    /**
     * Find client by unique token ID
     *
     * @param string $id
     * @return Client|null
     */
    public function getById(string $id): ?Client;

    /**
     * Get total client in repository
     *
     * @return int
     */
    public function count(): int;

    /**
     * Add a new client to the repository
     *
     * @return Client
     */
    public function add(): Client;

    /**
     * Remove client from repository
     *
     * @param Client $client
     */
    public function remove(Client $client): void;
}
