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

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Log;
use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Services\ClientRepository;
use React\Http\Message\Response;

class EventController extends BaseController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function handle(Request $request): Response
    {
        $request->isRole('server');

        $validatedData = $request->validate([
            'topic' => 'required|regex:/.+/',
            'payload' => 'nullable',
            'role' => 'regex:/.+/',
        ]);

        Log::debug('Server wants to emit', $validatedData);

        /** @var Client[] $clients */
        $clients = $this->clientRepository
            ->getClientsByTopicAndRole($validatedData['topic'], $validatedData['role']);

        foreach ($clients as $client) {
            $client->send([
                'topic' => $validatedData['topic'],
                'payload' => $validatedData['payload'],
            ]);
        }

        return $this->json(['status' => 'Event Send']);
    }
}
