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

use ISAAC\GazeHub\Exceptions\ConfigFileNotExistsException;
use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;

use function array_key_exists;
use function file_exists;
use function getenv;
use function sprintf;
use function strtoupper;

class ConfigRepositoryFilesystem implements ConfigRepository
{
    /**
     * @var mixed[]
     */
    private $config = [];

    /**
     * Load configuration file in memory, if path is null, /config/config.php is loaded
     *
     * @param string       $path                Path to config file to load
     * @throws ConfigFileNotExistsException     Thrown when the config file does not exist
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new ConfigFileNotExistsException(sprintf('No config file found at %s', $path));
        }

        $this->config = include($path);
        $this->loadEnvironmentVariables();
    }

    /**
     * Get a value from the loaded configuration
     *
     * @param string        $key            Key to load value for
     * @return mixed                        Value from configuration
     * @throws ConfigKeyNotFoundException   Thrown when key not found in config file
     */
    public function get(string $key)
    {
        if (!array_key_exists($key, $this->config)) {
            throw new ConfigKeyNotFoundException();
        }

        return $this->config[$key];
    }

    private function loadEnvironmentVariables(): void
    {
        foreach ($this->config as $key => $value) {
            $envValue = getenv('GAZEHUB_' . strtoupper($key));

            if ($envValue !== false) {
                $this->config[$key] = $envValue;
            }
        }
    }
}
