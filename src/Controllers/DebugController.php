<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Models\Request;
use React\Http\Message\Response;

class DebugController
{

    public function handle(): Response
    {
        return new Response(
            200,
            [ 'Content-Type' => 'text/html' ],
            file_get_contents(__DIR__ . '/../../public/debug.html')
        );
    }
}
