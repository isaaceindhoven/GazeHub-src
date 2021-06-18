<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Models;

use JsonSerializable;
use React\Stream\ThroughStream;

use function array_filter;
use function array_push;
use function in_array;
use function json_encode;

// phpcs:ignore ObjectCalisthenics.Metrics.MethodPerClassLimit.ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff
class Client implements JsonSerializable
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
    private $id;

    /**
     * @var string[]
     */
    private $topics = [];

    /**
     * @param string[] $roles
     * @param string $id
     */
    public function __construct(array $roles, string $id)
    {
        $this->roles = $roles;
        $this->id = $id;
        $this->stream = new ThroughStream(static function (array $data): string {
            return 'data: ' . json_encode($data) . "\n\n";
        });
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
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function equals(Client $client): bool
    {
        return $this->id === $client->getId();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'roles' => $this->roles,
            'topics' => $this->topics,
        ];
    }
}
