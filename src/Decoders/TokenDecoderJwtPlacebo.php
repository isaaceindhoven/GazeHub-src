<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Decoders;

use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use JsonException;

use function base64_decode;
use function count;
use function explode;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class TokenDecoderJwtPlacebo implements TokenDecoder
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

        try {
            $payload = json_decode($base64Decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TokenDecodeException();
        }

        if ($payload === null) {
            throw new TokenDecodeException();
        }

        return $payload;
    }
}
