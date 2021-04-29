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

use React\Http\Message\Response;

use function file_get_contents;

class HtmlFactory
{
    public function create(string $htmlFile, int $statusCode = 200): Response
    {
        $debugHtml = file_get_contents(__DIR__ . '/../../public/' . $htmlFile);

        return new Response(
            $statusCode,
            [ 'Content-Type' => 'text/html' ],
            $debugHtml === false ? '' : $debugHtml
        );
    }
}
