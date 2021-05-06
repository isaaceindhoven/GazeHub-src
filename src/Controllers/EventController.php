<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use ISAAC\GazeHub\Factories\JsonFactory;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use Psr\Log\LoggerInterface;
use React\Http\Message\Response;

class EventController
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        LoggerInterface $logger,
        JsonFactory $jsonFactory
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws DataValidationFailedException
     * @throws UnauthorizedException
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
            $client->getStream()->write([
                'topic' => $validatedData['topic'],
                'payload' => $validatedData['payload'],
            ]);
        }

        return $this->jsonFactory->create(['status' => 'Event Send']);
    }
}
