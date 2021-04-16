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

namespace ISAAC\GazeHub\Models;

use React\Stream\ThroughStream;

use function array_filter;
use function array_push;
use function in_array;

class Client
{
    /**
     * @var ThroughStream
     */
    public $stream;

    /**
     * @var string[]
     */
    public $roles;

    /**
     * @var string
     */
    public $tokenId;

    /**
     * @var string[]
     */
    public $topics = [];

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
}
