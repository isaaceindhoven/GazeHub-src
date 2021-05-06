<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
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

    public function __construct(
        LoopInterface $loop,
        ClientRepository $clientRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->loop = $loop;
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @return Response
     */
    public function handle(): Response
    {
        $client = $this->clientRepository->add();

        $client->getStream()->on(
            'close',
            function () use ($client): void {
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
