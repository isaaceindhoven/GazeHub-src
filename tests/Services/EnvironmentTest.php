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

namespace ISAAC\GazeHub\Tests\Services;

use ISAAC\GazeHub\Services\Environment;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;
use function putenv;

class EnvironmentTest extends TestCase
{
    public function testIfEnvIsNullWhenNotFound(): void
    {
        $env = Environment::get('NOT_FOUND');
        assertNull($env);
    }

    public function testIfDefaultValueIsSetWhenNotFound(): void
    {
        $env = Environment::get('NOT_FOUND', 'default');
        assertEquals('default', $env);
    }

    public function testIfEnvVariableGetterWorks(): void
    {
        putenv('name=Kevin');
        $env = Environment::get('name');
        assertEquals('Kevin', $env);
    }
}
