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

namespace GazeHub\Tests\Middlewares;

use GazeHub\Middlewares\JsonParserMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

class JsonParserMiddlewareTest extends TestCase
{
    public function testShouldSetParsedBodyWhenContentTypeIsJson(): void
    {
        // Arrange
        $middleware = new JsonParserMiddleware();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')->with('Content-Type')->willReturn('application/json');
        $request->method('getBody')->willReturn('{"test": "This is a test"}');
        $request
            ->expects(once())
            ->method('withParsedBody')
            ->with(['test' => 'This is a test'])
            ->willReturn($request);

        $callable = static function (ServerRequestInterface $req): ResponseInterface {
            return new Response(200);
        };

        // Act
        $response = $middleware->handle($request, $callable);

        // Assert
        assertEquals(200, $response->getStatusCode());
    }

    public function testShouldReturn400ResponseWhenJsonIsInvalid(): void
    {
        // Arrange
        $middleware = new JsonParserMiddleware();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('application/json');

        $request->method('getBody')
            ->willReturn('Invalid JSON');

        $request->expects(never())
            ->method('withParsedBody');

        $callable = static function (ServerRequestInterface $req): ResponseInterface {
            return new Response(200);
        };

        // Act
        $response = $middleware->handle($request, $callable);

        // Assert
        assertEquals(400, $response->getStatusCode());
    }
}
