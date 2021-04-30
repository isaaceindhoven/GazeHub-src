<?php

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
