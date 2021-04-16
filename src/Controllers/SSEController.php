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

use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Services\ClientRepository;
use React\Http\Message\Response;

class SSEController
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
        $request->isAuthorized();

        $payload = $request->getTokenPayload();

        $client = $this->clientRepository->add($payload['roles'], $payload['jti']);

        $scope = $this;

        $client->stream->on('close', static function () use ($scope, $client): void {
            $scope->clientRepository->remove($client);
        });

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $client->stream);
    }
}
