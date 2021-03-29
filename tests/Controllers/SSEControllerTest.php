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

namespace GazeHub\Tests\Controllers;

use function urlencode;

class SSEControllerTest extends ControllerTestCase
{
    public function testResponse401IfUnauthorized(): void
    {
        $this->req('/sse', 'GET')->assertHttpCode(401);
    }

    public function testResponse200IfAuthorized(): void
    {
        $this->req('/sse?token=' . urlencode($this->getClientToken()), 'GET')->assertHttpCode(200);
    }
}
