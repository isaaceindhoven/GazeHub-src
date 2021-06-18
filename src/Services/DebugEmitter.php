<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Services;

use ISAAC\GazeHub\Repositories\SubscriptionRepository;

use function sprintf;

class DebugEmitter
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param string $topic
     * @param mixed $payload
     */
    public function emit(string $topic, $payload): void
    {
        $topic = sprintf('GAZE_DEBUG_%s', $topic);
        $debugClients = $this->subscriptionRepository->getClientsByTopicAndRole($topic);

        foreach ($debugClients as $debugClient) {
            $debugClient->getStream()->write([
                'topic' => $topic,
                'payload' => $payload,
            ]);
        }
    }
}
