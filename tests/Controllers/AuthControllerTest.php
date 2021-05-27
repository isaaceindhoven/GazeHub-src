<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

use ISAAC\GazeHub\Repositories\ClientRepository;

class AuthControllerTest extends ControllerTestCase
{
    public function testAuthResponse400IfBodyIsMissingValues(): void
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

    public function testAuthResponse404IfClientNotFound(): void
    {
        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => 'non-existing', 'token' => 'some-token'])
            ->assertHttpCode(404);
    }

    public function testAuthResponse401WhenTokenIsInvalid(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);
        $client = $clientRepo->add();

        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => $client->getId(), 'token' => 'some-token'])
            ->assertHttpCode(401);
    }

    public function testAuthResponse400WhenTokenIsMissingRoles(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);
        $client = $clientRepo->add();

        $this
            ->req('/auth', 'POST')
            ->setBody(['id' => $client->getId(), 'token' => $this->generateToken([])])
            ->assertHttpCode(400);
    }

    public function testAuthResponse200IfTokenIsValid(): void
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

    public function testUnauthResponse401WhenIdIsMissingInHeader(): void
    {
        $this
            ->req('/auth', 'DELETE')
            ->assertHttpCode(401);
    }

    public function testUnauthResponse404IfClientNotFound(): void
    {
        $this
            ->req('/auth', 'DELETE')
            ->setHeaders(['Authorization' => 'Bearer id'])
            ->assertHttpCode(404);
    }

    public function testUnauthRemovedRolesFromClient(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);
        $client = $clientRepo->add();
        $client->setRoles(['admin']);

        $this
            ->req('/auth', 'DELETE')
            ->setHeaders(['Authorization' => 'Bearer ' . $client->getId()])
            ->assertHttpCode(200);

        self::assertEmpty($client->getRoles());
    }
}
