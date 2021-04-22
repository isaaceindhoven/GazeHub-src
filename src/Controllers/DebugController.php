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

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Repositories\ConfigRepository;
use React\Http\Message\Response;

/**
 * @codeCoverageIgnore
 */
class DebugController extends BaseController
{
    /**
     * @var bool $enableDebugPage
     */
    private $enableDebugPage;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->enableDebugPage = (bool) $configRepository->get('debug_page');
    }

    public function handle(): Response
    {
        if ($this->enableDebugPage) {
            return $this->html('debug.html');
        }

        return new Response(404);
    }
}
