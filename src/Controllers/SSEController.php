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

use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use React\Http\Message\Response;

class SSEController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(ClientRepository $clientRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws UnauthorizedException
     */
    public function handle(Request $request): Response
    {
        $request->isAuthorized();

        $payload = $request->getTokenPayload();

        $client = $this->clientRepository->add($payload['roles'], $payload['jti']);

        $client->getStream()->on(
            'close',
            function () use ($client): void {
                $this->subscriptionRepository->remove($client);
                $this->clientRepository->remove($client);
            }
        );

        return new Response(200, ['Content-Type' => 'text/event-stream'], $client->getStream());
    }
}
