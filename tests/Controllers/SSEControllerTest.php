<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

class SSEControllerTest extends ControllerTestCase
{
    public function testResponse401IfUnauthorized(): void
    {
        $this->req('/sse', 'GET')->assertHttpCode(401);
    }

    public function testResponse200IfAuthorized(): void
    {
        $this->req('/sse', 'GET')->asClient()->assertHttpCode(200);
    }
}
