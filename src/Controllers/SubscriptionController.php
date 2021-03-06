<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use ISAAC\GazeHub\Factories\JsonFactory;
use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use ISAAC\GazeHub\Services\DebugEmitter;
use React\Http\Message\Response;

class SubscriptionController
{
    /**
     *  @var ClientRepository
     */
    private $clientRepository;

    /**
     *  @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var DebugEmitter
     */
    private $debugEmitter;

    public function __construct(
        ClientRepository $clientRepository,
        SubscriptionRepository $subscriptionRepository,
        JsonFactory $jsonFactory,
        DebugEmitter $debugEmitter
    ) {
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->jsonFactory = $jsonFactory;
        $this->debugEmitter = $debugEmitter;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws UnauthorizedException
     * @throws DataValidationFailedException
     */
    public function create(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'topics' => 'required|array',
            'topics.*' => 'required|regex:/.+/',
        ]);

        $this->debugEmitter->emit('Subscribed', [
            'clientId' => $client->getId(),
            'topics' => $validatedData['topics'],
        ]);

        foreach ($validatedData['topics'] as $topic) {
            $this->subscriptionRepository->subscribe($client, $topic);
        }

        return $this->jsonFactory->create(['status' => 'subscribed'], 200);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws DataValidationFailedException
     * @throws UnauthorizedException
     */
    public function destroy(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'topics' => 'required|array',
            'topics.*' => 'required|regex:/.+/',
        ]);

        $this->debugEmitter->emit('Unsubscribed', [
            'clientId' => $client->getId(),
            'topics' => $validatedData['topics'],
        ]);

        foreach ($validatedData['topics'] as $topic) {
            $this->subscriptionRepository->unsubscribe($client, $topic);
        }

        return $this->jsonFactory->create(['status' => 'unsubscribed']);
    }

    /**
     * @param Request $request
     * @return Client
     * @throws UnauthorizedException
     */
    protected function getClient(Request $request): Client
    {
        $request->isAuthorized();
        $id = $request->getAuthTokenFromHeader();
        $client = $this->clientRepository->getById($id);
        if ($client === null) {
            throw new UnauthorizedException();
        }
        return $client;
    }
}
