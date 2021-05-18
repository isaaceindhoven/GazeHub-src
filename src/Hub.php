<?php

declare(strict_types=1);

namespace ISAAC\GazeHub;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use ISAAC\GazeHub\Middlewares\CorsMiddleware;
use ISAAC\GazeHub\Middlewares\JsonParserMiddleware;
use ISAAC\GazeHub\Providers\Provider;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server;

use function class_exists;
use function get_class;
use function sprintf;

class Hub
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Hub constructor.
     * @param string[] $providers
     * @param Container|null $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(array $providers = [], Container $container = null)
    {
        if ($container === null) {
            $container = new Container();
        }
        $this->container = $container;

        $this->logger = $this->container->get(LoggerInterface::class);
        $this->loadProviders($providers);

        $loop = Factory::create();
        $this->container->set(LoopInterface::class, $loop);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function run(): void
    {
        $config = $this->container->get(ConfigRepository::class);

        $host = $config->get('host');
        $port = $config->get('port');

        /** @var LoopInterface $loop */
        $loop = $this->container->get(LoopInterface::class);

        $socket = new Server(sprintf('%s:%s', $host, $port), $loop);

        $server = new HttpServer(
            $loop,
            [$this->container->get(CorsMiddleware::class), 'handle'],
            [$this->container->get(JsonParserMiddleware::class), 'handle'],
            [new Router($this->container), 'route']
        );

        $server->on('error', [$this, 'onError']);

        $server->listen($socket);

        $this->logger->info(sprintf('Server running on %s:%s', $host, $port));

        $loop->run();
    }

    public function onError(Exception $e): void
    {
        $message = $e->getMessage();
        if ($e->getPrevious() !== null && $e->getPrevious()->getMessage() !== '') {
            $message .= "\t" . sprintf('Previous error: %s' . $e->getPrevious()->getMessage());
        }

        $this->logger->error($message);
    }

    /**
     * @param string[] $providers
     */
    private function loadProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            if (!class_exists($provider)) {
                $this->logger->error(sprintf('Provider %s does not exist.', $provider));
                continue;
            }

            $provider = new $provider();

            if (!($provider instanceof Provider)) {
                $className = get_class($provider);
                $this->logger->error(sprintf('Provider %s is not an instance of Provider', $className));
                continue;
            }

            $provider->register($this->container);
        }
    }
}
