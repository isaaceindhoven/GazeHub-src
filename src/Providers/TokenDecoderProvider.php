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

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Exceptions\TokenDecoderClassNotFoundException;
use ISAAC\GazeHub\Repositories\ConfigRepository;

use function class_exists;

class TokenDecoderProvider implements Provider
{
    public function register(Container &$container): void
    {
        $configRepo = $container->get(ConfigRepository::class);

        $tokenDecoderClass = $configRepo->get('token_decoder');

        if (!class_exists($tokenDecoderClass)) {
            throw new TokenDecoderClassNotFoundException();
        }

        $container->set(TokenDecoder::class, $container->get($tokenDecoderClass));
    }
}
