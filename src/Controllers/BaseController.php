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

namespace GazeHub\Controllers;

use React\Http\Message\Response;

use function json_encode;

abstract class BaseController
{
    /**
     * @param string $text
     * @param string[] $headers
     * @param int $statusCode
     * @return Response
     */
    private function end(string $text, array $headers, int $statusCode): Response
    {
        return new Response($statusCode, $headers, $text);
    }

    /**
     * @param string[] $data
     * @param int $statusCode
     * @return Response
     */
    protected function json(array $data, int $statusCode = 200): Response
    {
        $data = json_encode($data);

        return $this->end($data === false ? '' : $data, [ 'Content-Type' => 'application/json' ], $statusCode);
    }
}
