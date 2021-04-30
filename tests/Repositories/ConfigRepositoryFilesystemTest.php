<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Repositories;

use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use ISAAC\GazeHub\Repositories\ConfigRepositoryFilesystem;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class ConfigRepositoryFilesystemTest extends TestCase
{
    public function testShouldLoadConfigFileWhenNoPathIsSupplied(): void
    {
        // Arrange
        $config = new ConfigRepositoryFilesystem();

        // Assert
        assertEquals('3333', $config->get('port'));
    }

    public function testShouldLoadCustomConfigFileWhenPathIsSupplied(): void
    {
        // Arrange
        $config = new ConfigRepositoryFilesystem(__DIR__ . '/../assets/gazehub.config.json');

        // Assert
        assertEquals('3334', $config->get('port'));
    }

    public function testShouldThrowExceptionWhenConfigKeyDoesNotExists(): void
    {
        // Arrange
        $this->expectException(ConfigKeyNotFoundException::class);
        $config = new ConfigRepositoryFilesystem(__DIR__ . '/../assets/gazehub.config.json');

        // Act
        $config->get('NON_EXISTING_KEY');

        // Assert
        // Nothing to assert
    }
}
