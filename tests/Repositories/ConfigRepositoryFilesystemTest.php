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
