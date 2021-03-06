<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

use ISAAC\GazeHub\Models\Client;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;
use React\Stream\ThroughStream;

use function PHPUnit\Framework\atLeastOnce;

class EventControllerTest extends ControllerTestCase
{
    public function testResponse401IfUnauthorized(): void
    {
        $this->req('/event', 'POST')->assertHttpCode(401);
    }

    public function testResponse401IfRoleIsWrong(): void
    {
        $this
            ->req('/event', 'POST')
            ->setHeaders(['Authorization' => 'Bearer ' . $this->generateToken(['role' => 'admin'])])
            ->assertHttpCode(401);
    }

    public function testResponse200IfValidRequest(): void
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test'])
            ->assertHttpCode(200);
    }

    public function testResponse400IfTopicMissing(): void
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => '', 'payload' => 'Test'])
            ->assertHttpCode(400);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['payload' => 'Test'])
            ->assertHttpCode(400);
    }

    public function testResponse200IfPayloadMissing(): void
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => ''])
            ->assertHttpCode(200);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => []])
            ->assertHttpCode(200);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated'])
            ->assertHttpCode(200);
    }

    public function testResponse200IfRoleIsMissing(): void
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test', 'role' => ''])
            ->assertHttpCode(200);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test'])
            ->assertHttpCode(200);
    }

    public function testIfClientSendIsCalled(): void
    {
        $subRepo = $this->createMock(SubscriptionRepositoryInMemory::class);
        $client = $this->createMock(Client::class);
        $client->expects(atLeastOnce())->method('getStream')->willReturn(new ThroughStream());

        $subRepo->method('getClientsByTopicAndRole')->willReturn([$client]);

        $this->container->set(SubscriptionRepository::class, $subRepo);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test'])
            ->assertHttpCode(200);
    }
}
