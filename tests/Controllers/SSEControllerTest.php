<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

use ISAAC\GazeHub\Controllers\SSEController;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\ClientRepository;
use React\EventLoop\LoopInterface;

use function React\Promise\Timer\resolve;

class SSEControllerTest extends ControllerTestCase
{
    public function testResponse200(): void
    {
        $this->req('/sse', 'GET')->assertHttpCode(200);
    }

    public function testClientRemovedOnStreamClosedEvent(): void
    {
        /** @var ClientRepository $clientRepo */
        $clientRepo = $this->container->get(ClientRepository::class);

        /** @var SSEController $controller */
        $controller = $this->container->get(SSEController::class);

        $response = $controller->handle();
        self::assertEquals(1, $clientRepo->count());

        $response->getBody()->close();

        self::assertEquals(0, $clientRepo->count());
    }

    public function testIdIsSendWhenResponseIsReturned(): void
    {
        /** @var SSEController $controller */
        $controller = $this->container->get(SSEController::class);

        /** @var LoopInterface $loop */
        $loop = $this->container->get(LoopInterface::class);

        $response = $controller->handle();
        $response->getBody()->input->on('data', static function ($data): void {
            self::assertNotEmpty($data);
        });

        resolve(0.1, $loop)->then(static function () use ($loop): void {
            $loop->stop();
        });
        $loop->run();
    }
}
