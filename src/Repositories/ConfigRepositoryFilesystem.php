<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Repositories;

use ISAAC\GazeHub\Exceptions\ConfigFileNotValidException;
use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use Nette\Utils\JsonException;

use function array_key_exists;
use function file_exists;
use function file_get_contents;
use function getenv;
use function json_decode;
use function strtoupper;
use function trim;

use const JSON_THROW_ON_ERROR;

class ConfigRepositoryFilesystem implements ConfigRepository
{
    /**
     * @var mixed[]
     */
    private $config = [];

    /**
     * Load configuration file in memory
     *
     * @param string $path Path to config file to load
     * @throws ConfigFileNotValidException      Thrown when Json file is invalid
     */
    public function __construct(string $path = null)
    {
        $this->loadDefaultConfiguration();

        if ($path !== null && file_exists($path)) {
            $this->loadJsonConfiguration($path);
        }

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

    private function loadDefaultConfiguration(): void
    {
        $this->config = include(__DIR__ . '/../../config/config.php');
    }

    /**
     * @param string $path
     * @throws ConfigFileNotValidException
     */
    private function loadJsonConfiguration(string $path): void
    {
        $jsonContent = file_get_contents($path);

        if ($jsonContent === false || trim($jsonContent) === '') {
            return;
        }

        try {
            $jsonConfig = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ConfigFileNotValidException();
        }

        foreach ($this->config as $key => $value) {
            if (array_key_exists($key, $jsonConfig)) {
                $this->config[$key] = $jsonConfig[$key];
            }
        }
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
