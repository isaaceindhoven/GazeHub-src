<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests;

use ISAAC\GazeHub\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\once;

class RouterTest extends BaseTest
{
    /**
     * @var Router
     */
    private $router;

    public function __construct()
    {
        parent::__construct();
        $this->router = $this->container->get(Router::class);
    }

    /**
     * @return ServerRequestInterface
     */
    private function visitUrl(string $url, string $method = 'GET')
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->expects(once())->method('getPath')->willReturn($url);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(once())->method('getUri')->willReturn($uri);
        $request->expects(once())->method('getMethod')->willReturn($method);

        return $request;
    }

    public function testShouldReturnNotFoundForNonExistingRoute(): void
    {
        // Arrange
        $request = $this->visitUrl('/does-not-exist');

        // Act
        $response = $this->router->route($request);

        // Assert
        assertEquals(404, $response->getStatusCode());
    }
}
