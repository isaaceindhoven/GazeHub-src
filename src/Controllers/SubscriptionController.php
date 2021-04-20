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

use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\UnAuthorizedException;
use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\IClientRepository;
use ISAAC\GazeHub\Repositories\ISubscriptionRepository;
use React\Http\Message\Response;

class SubscriptionController extends BaseController
{
    /**
     *  @var IClientRepository
     */
    private $clientRepository;

    /**
     *  @var ISubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(IClientRepository $clientRepository, ISubscriptionRepository $subscriptionRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws UnAuthorizedException
     * @throws DataValidationFailedException
     */
    public function create(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'topics' => 'required|array',
            'topics.*' => 'required|regex:/.+/',
        ]);

        foreach ($validatedData['topics'] as $topic) {
            $this->subscriptionRepository->subscribe($client, $topic);
        }

        return $this->json(['status' => 'subscribed'], 200);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws DataValidationFailedException
     * @throws UnAuthorizedException
     */
    public function destroy(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'topics' => 'required|array',
            'topics.*' => 'required|regex:/.+/',
        ]);

        foreach ($validatedData['topics'] as $topic) {
            $this->subscriptionRepository->unsubscribe($client, $topic);
        }

        return $this->json(['status' => 'unsubscribed']);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws UnAuthorizedException
     */
    public function ping(Request $request): Response
    {
        $client = $this->getClient($request);
        $client->send(['pong']);
        return new Response();
    }

    /**
     * @param Request $request
     * @return Client
     * @throws UnAuthorizedException
     */
    protected function getClient(Request $request): Client
    {
        $request->isAuthorized();
        $jti = $request->getTokenPayload()['jti'];
        $client = $this->clientRepository->getByTokenId($jti);
        if ($client === null) {
            throw new UnAuthorizedException();
        }
        return $client;
    }
}
