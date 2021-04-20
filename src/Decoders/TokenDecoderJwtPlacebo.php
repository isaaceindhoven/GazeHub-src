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

use function base64_decode;
use function count;
use function explode;
use function json_decode;

class TokenDecoderJwtPlacebo implements ITokenDecoder
{
    /**
     * @param string $token
     * @return mixed[]
     * @throws TokenDecodeException
     */
    public function decode(string $token): array
    {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) !== 3) {
            throw new TokenDecodeException();
        }
        $base64Data = $tokenParts[1];

        $base64Decoded = base64_decode($base64Data, true);

        if ($base64Decoded === false) {
            throw new TokenDecodeException();
        }

        $payload = json_decode($base64Decoded, true);

        if ($payload === null) {
            throw new TokenDecodeException();
        }

        return $payload;
    }
}
