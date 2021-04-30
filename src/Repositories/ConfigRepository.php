<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;

interface ConfigRepository
{
    /**
     * Get a value from the loaded configuration
     *
     * @param string $key Key to load value for
     * @return mixed                        Value from configuration
     * @throws ConfigKeyNotFoundException   Thrown when key not found in config file
     */
    public function get(string $key);
}
