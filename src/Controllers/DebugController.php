<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use React\Http\Message\Response;

use function file_get_contents;

class DebugController
{
    public function handle(): Response
    {
        $debugPage = file_get_contents(__DIR__ . '/../../public/debug.html');

        if ($debugPage === false) {
            return new Response(404);
        }

        return new Response(
            200,
            [ 'Content-Type' => 'text/html' ],
            $debugPage
        );
    }
}
