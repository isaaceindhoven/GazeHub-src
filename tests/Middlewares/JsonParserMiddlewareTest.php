<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Middlewares;

use ISAAC\GazeHub\Middlewares\JsonParserMiddleware;
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
