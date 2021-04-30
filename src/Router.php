<?php

declare(strict_types=1);

namespace ISAAC\GazeHub;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use ISAAC\GazeHub\Helpers\Json;
use ISAAC\GazeHub\Models\Request;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use function array_key_exists;
use function call_user_func;

class Router
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string[][]
     */
    private $routes;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->routes = require(__DIR__ . '/../config/routes.php');
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function route(ServerRequestInterface $request): Response
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $handler = $this->getHandler($method, $path);

        if ($handler === null) {
            return new Response(404);
        }

        $req = new Request($this->container->get(TokenDecoder::class), $request);

        return $this->handle($handler, $req); // @phpstan-ignore-line
    }

    /**
     * @param Callable $handler
     * @param Request $req
     * @return Response
     */
    private function handle($handler, Request $req): Response
    {
        try {
            return call_user_func($handler, $req);
        } catch (DataValidationFailedException $e) {
            return $this->jsonResponse(400, $e->errors);
        } catch (UnauthorizedException | TokenDecodeException $e) {
            return $this->jsonResponse(401);
        }
    }

    /**
     * @param string $method
     * @param string $path
     * @return null|mixed[]
     */
    private function getHandler(string $method, string $path)
    {
        $methodExists = array_key_exists($method, $this->routes);

        if (!$methodExists) {
            return null;
        }

        $pathInMethodExists = array_key_exists($path, $this->routes[$method]);

        if (!$pathInMethodExists) {
            return null;
        }

        [$controller, $handler] = $this->routes[$method][$path];

        return [$this->container->get($controller), $handler];
    }

    /**
     * @param integer $code
     * @param mixed $data
     * @return Response
     */
    private function jsonResponse(int $code, $data = null): Response
    {
        return new Response(
            $code,
            ['Content-Type' => 'application/json'],
            Json::encode($data, '')
        );
    }
}
