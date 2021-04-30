<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Models;

use React\Stream\ThroughStream;

use function array_filter;
use function array_push;
use function in_array;
use function json_encode;

class Client
{
    /**
     * @var ThroughStream
     */
    private $stream;

    /**
     * @var string[]
     */
    private $roles;

    /**
     * @var string
     */
    private $tokenId;

    /**
     * @var string[]
     */
    private $topics = [];

    /**
     * @param string[] $roles
     * @param string $tokenId
     */
    public function __construct(array $roles, string $tokenId)
    {
        $this->roles = $roles;
        $this->tokenId = $tokenId;
        $this->stream = new ThroughStream(static function (array $data): string {
            return 'data: ' . json_encode($data) . "\n\n";
        });
    }

    /**
     * @param mixed[] $data
     */
    public function send(array $data): void
    {
        $this->stream->write($data);
    }

    /**
     * @param string[] $topics
     * @return void
     */
    public function addTopics(array $topics)
    {
        array_push($this->topics, ...$topics);
    }

    /**
     * @param string[] $topics
     * @return void
     */
    public function removeTopics(array $topics)
    {
        $this->topics = array_filter($this->topics, static function ($topic) use ($topics): bool {
            return !in_array($topic, $topics, true);
        });
    }

    public function hasTopic(string $topic): bool
    {
        return in_array($topic, $this->topics, true);
    }

    public function getStream(): ThroughStream
    {
        return $this->stream;
    }

    /**
     * @return string[]
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    public function equals(Client $client): bool
    {
        return $this->tokenId === $client->getTokenId();
    }
}
