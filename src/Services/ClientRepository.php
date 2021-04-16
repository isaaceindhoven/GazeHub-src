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

use ISAAC\GazeHub\Log;
use ISAAC\GazeHub\Models\Client;
use React\Stream\ThroughStream;

use function array_filter;
use function array_push;
use function count;
use function in_array;
use function json_encode;

class ClientRepository
{
    /**
     * @var Client[]
     */
    private $clients = [];

    public function __construct()
    {
        $this->clients = [];
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
            if ($client->tokenId === $tokenId) {
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
        $client = new Client();
        $client->roles = $roles;
        $client->tokenId = $tokenId;
        $client->stream = new ThroughStream(static function (array $data): string {
            Log::debug('Sending data to client:', $data);
            return 'data: ' . json_encode($data) . "\n\n";
        });

        array_push($this->clients, $client);
        Log::debug('Connected clients', count($this->clients));

        return $client;
    }

    /**
     * Remove client from repository, the stream is not closed automatically.
     *
     * @param Client        $clientToRemove
     */
    public function remove(Client $clientToRemove): void
    {
        foreach ($this->clients as $index => $client) {
            if ($client->tokenId === $clientToRemove->tokenId) {
                unset($this->clients[$index]);
                break;
            }
        }
        Log::debug('Connected clients', count($this->clients));
    }

    /**
     * @param string $topic
     * @param string $role
     * @return Client[]
     */
    public function getClientsByTopicAndRole(string $topic, string $role = null): array
    {
        return array_filter(
            $this->clients,
            static function (Client $client) use ($topic, $role): bool {

                $clientSubscribedToTopic = in_array($topic, $client->topics, true);
                $roleSpecified = $role !== null && $role !== '';

                if ($roleSpecified) {
                    return $clientSubscribedToTopic && in_array($role, $client->roles, true);
                }

                return $clientSubscribedToTopic;
            }
        );
    }
}
