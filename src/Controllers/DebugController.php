<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use ISAAC\GazeHub\Factories\JsonFactory;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use React\Http\Message\Response;

use function array_values;
use function file_get_contents;

class DebugController
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    public function __construct(
        ClientRepository $clientRepository,
        ConfigRepository $configRepository,
        JsonFactory $jsonFactory
    ) {
        $this->clientRepository = $clientRepository;
        $this->jsonFactory = $jsonFactory;

        try {
            $this->enabled = (bool) $configRepository->get('debug_page');
        } catch (ConfigKeyNotFoundException $e) {
            $this->enabled = false;
        }
    }

    public function handle(): Response
    {
        $debugPage = file_get_contents(__DIR__ . '/../../public/debug.html');

        if ($debugPage === false || !$this->enabled) {
            return new Response(404);
        }

        return new Response(
            200,
            [ 'Content-Type' => 'text/html' ],
            $debugPage
        );
    }

    public function clients(): Response
    {
        if (!$this->enabled) {
            return new Response(404);
        }

        return $this->jsonFactory->create(array_values($this->clientRepository->getAll()));
    }
}
