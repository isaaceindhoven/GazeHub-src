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

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Helpers\Json;
use React\Http\Message\Response;

use function file_get_contents;

abstract class BaseController
{
    /**
     * @param string[] $data
     * @param int $statusCode
     * @return Response
     */
    protected function json(array $data, int $statusCode = 200): Response
    {
        return new Response(
            $statusCode,
            [ 'Content-Type' => 'application/json' ],
            Json::encode($data, '')
        );
    }

    protected function html(string $htmlFile, int $statusCode = 200): Response
    {
        $debugHtml = file_get_contents(__DIR__ . '/../../public/' . $htmlFile);

        return new Response(
            $statusCode,
            [ 'Content-Type' => 'text/html' ],
            $debugHtml === false ? '' : $debugHtml
        );
    }
}
