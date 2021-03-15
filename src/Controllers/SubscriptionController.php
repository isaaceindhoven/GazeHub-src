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

namespace GazeHub\Controllers;

use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Models\Subscription;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

class SubscriptionController extends BaseController
{
    /**
     *  @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     *  @var ClientRepository
     */
    private $clientRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, ClientRepository $clientRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->clientRepository = $clientRepository;
    }

    public function create(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'callbackId' => 'required|string',
            'topics' => 'required|array:string|not_empty',
        ]);

        $this->subscriptionRepository->add($client, $validatedData['topics'], $validatedData['callbackId']);

        return $this->json(['status' => 'subscribed'], 200);
    }

    public function destroy(Request $request): Response
    {
        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        if (!is_array($request->getParsedBody()) || count($request->getParsedBody()) === 0) {
            return new Response(400, [], 'Missing topics');
        }

        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'topics' => 'required|array:string|not_empty',
        ]);

        $this->subscriptionRepository->remove($client, $validatedData['topics']);

        return $this->json(['status' => 'unsubscribed']);
    }

    protected function getClient(Request $request): Client
    {
        $request->isAuthorized();

        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);

        if (!$client) {
            throw new UnAuthorizedException();
        }

        return $client;
    }
}
