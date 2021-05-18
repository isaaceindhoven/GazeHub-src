<?php

declare(strict_types=1);

namespace ISAAC\GazeHub;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\GazeException;
use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use ISAAC\GazeHub\Helpers\Json;
use ISAAC\GazeHub\Models\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\Http\Message\Response;

use function array_key_exists;
use function call_user_func;
use function method_exists;
use function sprintf;

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

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Router constructor.
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->routes = require(__DIR__ . '/../config/routes.php');
        $this->logger = $this->container->get(LoggerInterface::class);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function route(ServerRequestInterface $request): Response
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        try {
            $handler = $this->getHandler($method, $path);
        } catch (DependencyException | NotFoundException $e) {
            return new Response(404);
        }

        try {
            $req = new Request($this->container->get(TokenDecoder::class), $request);
        } catch (DependencyException | NotFoundException $e) {
            $this->logger->critical('Cannot get TokenDecoder instance from DI container');
            return new Response(500);
        }

        return $this->handle($handler, $req);
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
            return new Response(401);
        } catch (GazeException $e) {
            $this->logger->error($e->getMessage());
            return new Response(500);
        }
    }

    /**
     * @param string $method
     * @param string $path
     * @return Callable
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function getHandler(string $method, string $path)
    {
        if (!$this->routeExists($method, $path)) {
            throw new NotFoundException();
        }

        [$controller, $handler] = $this->routes[$method][$path];

        $instance = $this->container->get($controller);

        if (!method_exists($instance, $handler)) {
            $this->logger->critical(sprintf(
                'Controller \'%s\' is missing method \'%s\' as defined in config/routes.php',
                $controller,
                $handler
            ));
            throw new NotFoundException();
        }

        return static function (Request $request) use ($instance, $handler): Response {
            return $instance->$handler($request); // @phpstan-ignore-line
        };
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

    private function routeExists(string $method, string $path): bool
    {
        $methodExists = array_key_exists($method, $this->routes);

        if (!$methodExists) {
            return false;
        }

        $pathInMethodExists = array_key_exists($path, $this->routes[$method]);

        if (!$pathInMethodExists) {
            return false;
        }

        return true;
    }
}
