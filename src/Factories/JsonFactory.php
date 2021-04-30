<?php

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
