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

namespace ISAAC\GazeHub\Tests\Repositories;

use ISAAC\GazeHub\Exceptions\ConfigFileNotExistsException;
use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use ISAAC\GazeHub\Repositories\ConfigRepositoryFilesystem;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;

class ConfigRepositoryFilesystemTest extends TestCase
{
    public function testShouldAutoloadConfigWhenNoPathIsSupplied(): void
    {
        // Arrange
        $config = new ConfigRepositoryFilesystem(__DIR__ . '/../../config/config.php');

        // Assert
        assertNotEmpty($config->get('server_port'));
    }

    public function testShouldLoadCustomConfigFileWhenPathIsSupplied(): void
    {
        // Arrange
        $config = new ConfigRepositoryFilesystem(__DIR__ . '/../assets/testConfig.php');

        // Assert
        assertEquals('test_value', $config->get('test_key'));
    }

    public function testShouldThrowExceptionWhenConfigFileDoesNotExists(): void
    {
        // Arrange
        $this->expectException(ConfigFileNotExistsException::class);
        $config = new ConfigRepositoryFilesystem('NON_EXISTING_PATH');

        // Assert
        // Nothing to assert
    }

    public function testShouldThrowExceptionWhenConfigKeyDoesNotExists(): void
    {
        // Arrange
        $this->expectException(ConfigKeyNotFoundException::class);
        $config = new ConfigRepositoryFilesystem(__DIR__ . '/../assets/testConfig.php');

        // Act
        $config->get('NON_EXISTING_KEY');

        // Assert
        // Nothing to assert
    }
}
