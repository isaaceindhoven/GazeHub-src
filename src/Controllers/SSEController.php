<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use ISAAC\GazeHub\Services\DebugEmitter;
use React\EventLoop\LoopInterface;
use React\Http\Message\Response;

use function React\Promise\Timer\resolve;

class SSEController
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var DebugEmitter
     */
    private $debugEmitter;

    public function __construct(
        LoopInterface $loop,
        ClientRepository $clientRepository,
        SubscriptionRepository $subscriptionRepository,
        DebugEmitter $debugEmitter
    ) {
        $this->loop = $loop;
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->debugEmitter = $debugEmitter;
    }

    /**
     * @return Response
     */
    public function handle(): Response
    {
        $client = $this->clientRepository->add();

        $this->debugEmitter->emit('ClientConnected', ['id' => $client->getId()]);

        $client->getStream()->on(
            'close',
            function () use ($client): void {

                $this->debugEmitter->emit('ClientDisconnected', ['id' => $client->getId()]);

                $this->subscriptionRepository->remove($client);
                $this->clientRepository->remove($client);
            }
        );

        $response = new Response(200, ['Content-Type' => 'text/event-stream'], $client->getStream());

        resolve(0, $this->loop)->then(static function () use ($client): void {
            $client->getStream()->write([
                'id' => $client->getId(),
            ]);
        });

        return $response;
    }
}
