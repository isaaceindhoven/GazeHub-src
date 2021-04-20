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
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\ISubscriptionRepository;
use Psr\Log\LoggerInterface;
use React\Http\Message\Response;

class EventController extends BaseController
{
    /**
     * @var ISubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ISubscriptionRepository $subscriptionRepository, LoggerInterface $logger)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws DataValidationFailedException
     * @throws UnAuthorizedException
     */
    public function handle(Request $request): Response
    {
        $request->isRole('server');

        $validatedData = $request->validate([
            'topic' => 'required|regex:/.+/',
            'payload' => 'nullable',
            'role' => 'regex:/.+/',
        ]);

        $this->logger->debug('Server wants to emit', $validatedData);

        $clients = $this->subscriptionRepository->getClientsByTopicAndRole(
            $validatedData['topic'],
            $validatedData['role']
        );

        foreach ($clients as $client) {
            $this->logger->debug('Sending data to client', $validatedData);
            $client->send([
                'topic' => $validatedData['topic'],
                'payload' => $validatedData['payload'],
            ]);
        }

        return $this->json(['status' => 'Event Send']);
    }
}
