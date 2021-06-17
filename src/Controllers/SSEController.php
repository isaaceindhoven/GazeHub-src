<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Models\Request;
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
    public function handle(Request $request): Response
    {
        $client = $this->clientRepository->add();

        $debugClients = $this->subscriptionRepository->getClientsByTopicAndRole('GAZE_DEBUG_ClientConnected');

        foreach ($debugClients as $debugClient) {
            $debugClient->getStream()->write([
                'topic' => 'GAZE_DEBUG_ClientConnected',
                'payload' => [
                    'id' => $client->getId(),
                    'ip' => $request->getIp(),
                ],
            ]);
        }

        $client->getStream()->on(
            'close',
            function () use ($client): void {

                $debugClients = $this->subscriptionRepository->getClientsByTopicAndRole('GAZE_DEBUG_ClientDisconnected');

                foreach ($debugClients as $debugClient) {
                    $debugClient->getStream()->write([
                        'topic' => 'GAZE_DEBUG_ClientDisconnected',
                        'payload' => [
                            'id' => $client->getId(),
                        ],
                    ]);
                }

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
