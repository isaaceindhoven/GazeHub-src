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