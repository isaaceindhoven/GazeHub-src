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
use ISAAC\GazeHub\Decoders\ITokenDecoder;
use ISAAC\GazeHub\Exceptions\TokenDecoderClassNotFoundException;
use ISAAC\GazeHub\Repositories\IConfigRepository;

use function class_exists;

class TokenDecoderProvider implements IProvider
{
    public function register(Container &$container): void
    {
        $configRepo = $container->get(IConfigRepository::class);

        $tokenDecoderClass = $configRepo->get('token_decoder');

        if (!class_exists($tokenDecoderClass)) {
            throw new TokenDecoderClassNotFoundException();
        }

        $container->set(ITokenDecoder::class, $container->get($tokenDecoderClass));
    }
}
