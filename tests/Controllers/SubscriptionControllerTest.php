<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

class SubscriptionControllerTest extends ControllerTestCase
{
    public function testSubscribeResponse401IfUnauthorized(): void
    {
        $this->req('/subscription', 'POST')->assertHttpCode(401);
    }

    public function testUnsubscribeResponse401IfUnauthorized(): void
    {
        $this->req('/subscription', 'DELETE')->assertHttpCode(401);
    }

    public function testSubscribeResponse200IfAuthorized(): void
    {
        $this
            ->req('/subscription', 'POST')
            ->registerClient('client1')
            ->asClient('client1')
            ->setBody(['callbackId' => 'abc', 'topics' => ['ProductCreated']])
            ->assertHttpCode(200);
    }

    public function testSubscribeResponse400IfNoTopic(): void
    {
        $this
            ->req('/subscription', 'POST')
            ->registerClient('client1')
            ->asClient('client1')
            ->setBody(['callbackId' => 'abc', 'topics' => []])
            ->assertHttpCode(400);
    }

    public function testUnsubscribeResponse200IfAuthorized(): void
    {
        $this
            ->req('/subscription', 'DELETE')
            ->registerClient('client1')
            ->asClient('client1')
            ->setBody(['topics' => ['ProductCreated']])
            ->assertHttpCode(200);
    }

    public function testSubscribeResponse401IfClientNotRegistered(): void
    {
        $this
            ->req('/subscription', 'POST')
            ->asClient('client1')
            ->setBody(['callbackId' => 'abc', 'topics' => ['ProductCreated']])
            ->assertHttpCode(401);
    }

    public function testUnsubscribeResponse401IfClientNotRegistered(): void
    {
        $this
            ->req('/subscription', 'DELETE')
            ->asClient('client1')
            ->setBody(['topics' => ['ProductCreated']])
            ->assertHttpCode(401);
    }

    public function testPingResponse401IfClientNotRegistered(): void
    {
        $this
            ->req('/ping', 'GET')
            ->asClient('client1')
            ->assertHttpCode(401);
    }

    public function testPingResponse200IfAuthorized(): void
    {
        $this
            ->req('/ping', 'GET')
            ->registerClient('client1')
            ->asClient('client1')
            ->assertHttpCode(200);
    }

    public function testPingResponse401WithInvalidJwt(): void
    {
        $this
            ->req('/ping', 'GET')
            ->setHeaders(['Authorization' => 'Bearer NOTVALID.NOTVALID.NOTVALID'])
            ->assertHttpCode(401);
    }
}
