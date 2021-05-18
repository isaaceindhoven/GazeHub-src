<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Providers;

use DI\Container;
use ISAAC\GazeHub\Providers\Provider;

class ValidProvider implements Provider
{
    public function register(Container &$container): void
    {
        $container->set('ValidProviderTest', 'registered');
    }
}
