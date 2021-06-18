<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use React\Http\Message\Response;

use function file_get_contents;

class DebugController
{
    /**
     * @var bool
     */
    private $enabled;

    public function __construct(ConfigRepository $configRepository)
    {
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
}
