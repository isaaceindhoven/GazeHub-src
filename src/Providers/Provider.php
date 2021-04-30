<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;

interface Provider
{
    public function register(Container &$container): void;
}
