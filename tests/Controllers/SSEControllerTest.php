<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Controllers;

class SSEControllerTest extends ControllerTestCase
{
    public function testResponse200(): void
    {
        $this->req('/sse', 'GET')->assertHttpCode(200);
    }
}
