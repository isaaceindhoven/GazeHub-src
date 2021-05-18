<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

use ISAAC\GazeHub\Repositories\ClientRepository;

class AuthControllerTest extends ControllerTestCase
{
    public function testResponse400IfBodyIsMissingValues(): void
    {
        $this
            ->req('/auth', 'POST')
            ->setBody([])
            ->assertHttpCode(400);

        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => '', 'token' => ''])
            ->assertHttpCode(400);
    }

    public function testResponse404IfClientNotFound(): void
    {
        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => 'non-existing', 'token' => 'some-token'])
            ->assertHttpCode(404);
    }

    public function testResponse401WhenTokenIsInvalid(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);
        $client = $clientRepo->add();

        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => $client->getId(), 'token' => 'some-token'])
            ->assertHttpCode(401);
    }

    public function testResponse400WhenTokenIsMissingRoles(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);
        $client = $clientRepo->add();

        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => $client->getId(), 'token' => $this->generateToken([])])
            ->assertHttpCode(400);
    }

    public function testResponse200IfTokenIsValid(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);
        $client = $clientRepo->add();
        $roles = ['admin', 'user'];

        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => $client->getId(), 'token' => $this->generateToken(['roles' => $roles])])
            ->assertHttpCode(200);

        self::assertEquals($roles, $client->getRoles());
    }
}
