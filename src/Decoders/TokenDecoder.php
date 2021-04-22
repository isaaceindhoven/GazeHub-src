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

namespace ISAAC\GazeHub\Decoders;

use ISAAC\GazeHub\Exceptions\TokenDecodeException;

interface TokenDecoder
{
    /**
     * Decode token and return associated data
     *
     * @param string $token
     * @return mixed[]
     * @throws TokenDecodeException
     */
    public function decode(string $token): array;
}
