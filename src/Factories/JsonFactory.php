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

namespace ISAAC\GazeHub\Factories;

use ISAAC\GazeHub\Helpers\Json;
use React\Http\Message\Response;

class JsonFactory
{
    /**
     * @param string[] $data
     * @param int $statusCode
     * @return Response
     */
    public function create(array $data, int $statusCode = 200): Response
    {
        return new Response(
            $statusCode,
            [ 'Content-Type' => 'application/json' ],
            Json::encode($data, '')
        );
    }
}
