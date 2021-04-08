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

namespace ISAAC\GazeHub\Tests\Middlewares;

use ISAAC\GazeHub\Middlewares\CorsMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use RingCentral\Psr7\MessageTrait;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class CorsMiddlewareTest extends TestCase
{
    public function testShouldAddCorsHeadersToOptionsRequestAndRespondWith204(): void
    {
        // Arrange
        $middleware = new CorsMiddleware();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('options');
        $callable = static function (ServerRequestInterface $req): ServerRequestInterface {
            return $req;
        };

        // Act
        $response = $middleware->handle($request, $callable);

        // Assert
        assertEquals(204, $response->getStatusCode());
        assertArrayHasKey('Access-Control-Allow-Origin', $response->getHeaders());
        assertArrayHasKey('Access-Control-Allow-Methods', $response->getHeaders());
        assertArrayHasKey('Access-Control-Allow-Headers', $response->getHeaders());
    }

    public function testShouldAddCorsHeadersToOtherRequestAndForwardRequest(): void
    {
        // Arrange
        $middleware = new CorsMiddleware();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('get');
        $callable = static function (ServerRequestInterface $req): MessageTrait {
            $resp = new Response(500);
            return $resp->withHeader('X-Test-Header', 'This is a test');
        };

        // Act
        $response = $middleware->handle($request, $callable);

        // Assert
        assertEquals(500, $response->getStatusCode());
        assertArrayHasKey('X-Test-Header', $response->getHeaders());
        assertArrayHasKey('Access-Control-Allow-Origin', $response->getHeaders());
        assertArrayHasKey('Access-Control-Allow-Methods', $response->getHeaders());
        assertArrayHasKey('Access-Control-Allow-Headers', $response->getHeaders());
    }
}
