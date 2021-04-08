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

namespace ISAAC\GazeHub\Tests\Controllers;

use DI\Container;
use ISAAC\GazeHub\Router;
use ISAAC\GazeHub\Services\ClientRepository;
use ISAAC\GazeHub\Services\ConfigRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use React\Http\Message\Response;

use function array_key_exists;
use function base64_encode;
use function explode;
use function is_string;
use function json_encode;
use function parse_url;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\returnCallback;
use function uniqid;

use const PHP_URL_PATH;
use const PHP_URL_QUERY;

// phpcs:ignore ObjectCalisthenics.Metrics.MethodPerClassLimit.ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff
class ControllerTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string;
     */
    private $method;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * @var string[]
     */
    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $headers;

    /**
     * @var mixed[]
     */
    private $body;

    public function __construct()
    {
        parent::__construct();
        $this->container = new Container();
    }

    protected function setUp(): void
    {
        $this->response = null;
        $this->headers = [];
        $this->body = [];
    }

    protected function req(string $url, string $method): self
    {
        $this->setUp();
        $this->url = $url;
        $this->method = $method;
        return $this;
    }

    /**
     * @param string[] $headers
     * @return $this
     */
    protected function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param mixed[] $payload
     * @return string
     */
    protected function generateToken(array $payload): string
    {
        $payload = json_encode($payload);
        return 'KEEPME.' . base64_encode($payload === false ? '' : $payload) . '.KEEPME';
    }

    protected function asServer(): self
    {
        $this->setHeaders(['Authorization' => 'Bearer ' . $this->generateToken(['role' => 'server'])]);
        return $this;
    }

    protected function registerClient(string $jti): self
    {
        $clientRepo = $this->container->get(ClientRepository::class);
        $clientRepo->add([], $jti);
        return $this;
    }

    protected function asClient(string $jti = null): self
    {
        $this->setHeaders(['Authorization' => 'Bearer ' . $this->getClientToken($jti) ]);
        return $this;
    }

    protected function getClientToken(string $jti = null): string
    {
        if ($jti === null) {
            $jti = uniqid();
        }
        return $this->generateToken(['roles' => [], 'jti' => $jti]);
    }

    /**
     * @param mixed[] $body
     * @return $this
     */
    protected function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    protected function do(): self
    {
        $configRepo = new ConfigRepository();
        $configRepo->loadConfig(__DIR__ . '/../assets/testConfig.php');

        $this->container->set(ConfigRepository::class, $configRepo);

        $router = new Router($this->container);
        $this->response = $router->route($this->buildOriginalRequest()); // @phpstan-ignore-line

        return $this;
    }

    private function buildOriginalRequest(): MockObject
    {
        $originalRequest = $this->createMock(ServerRequestInterface::class);

        $originalRequest->method('getMethod')->willReturn($this->method);
        $originalRequest->method('getParsedBody')->willReturn($this->body);
        $originalRequest->method('getQueryParams')->willReturn($this->getParsedQuery());
        $scope = $this;
        $originalRequest
            ->method('getHeaderLine')
            ->will(returnCallback(static function ($key) use ($scope): string {
                if (!array_key_exists($key, $scope->headers)) {
                    return '';
                }
                return $scope->headers[$key];
            }));

        $uriMock = $this->createMock(UriInterface::class);
        $uriMock->method('getPath')->willReturn(parse_url($this->url, PHP_URL_PATH));

        $originalRequest->method('getUri')->willReturn($uriMock);

        return $originalRequest;
    }

    protected function assertHttpCode(int $code): void
    {
        if ($this->response === null) {
            $this->do();
        }

        if ($this->response !== null) {
            assertEquals($code, $this->response->getStatusCode());
        }
    }

    /**
     * @return string[]
     */
    private function getParsedQuery(): array
    {
        $queryString = parse_url($this->url, PHP_URL_QUERY);

        if (!is_string($queryString)) {
            return [];
        }

        $params = explode('&', $queryString);
        $queryParams = [];
        foreach ($params as $param) {
            [$key, $val] = explode('=', $param);
            $queryParams[$key] = $val;
        }
        return $queryParams;
    }
}
