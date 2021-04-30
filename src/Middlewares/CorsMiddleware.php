<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use function strtoupper;

class CorsMiddleware
{
    public function handle(ServerRequestInterface $request, callable $next): Response
    {
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $this->addCorsHeaders($request, new Response(204));
        }

        return $this->addCorsHeaders($request, $next($request));
    }

    private function addCorsHeaders(ServerRequestInterface $request, Response $response): Response
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('Origin'))
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Authorization, Content-Type');
    }
}
