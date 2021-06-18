<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Services;

use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;

use function sprintf;

class DebugEmitter
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        ConfigRepository $configRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        try {
            $this->enabled = (bool) $configRepository->get('debug_page');
        } catch (ConfigKeyNotFoundException $e) {
            $this->enabled = false;
        }
    }

    /**
     * @param string $topic
     * @param mixed $payload
     */
    public function emit(string $topic, $payload): void
    {
        if (!$this->enabled) {
            return;
        }

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
