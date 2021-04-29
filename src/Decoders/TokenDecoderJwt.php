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

use Exception;
use Firebase\JWT\JWT;
use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use ISAAC\GazeHub\Repositories\ConfigRepository;

use function explode;

class TokenDecoderJwt implements TokenDecoder
{
    /**
     * @var string
     */
    private $publicKeyContent;

    /**
     * @var string
     */
    private $algorithm;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->algorithm = $configRepository->get('jwt_alg');
        $this->publicKeyContent = $configRepository->get('jwt_public_key');
    }

    /**
     * @param string $token
     * @return mixed[]
     * @throws TokenDecodeException
     */
    public function decode(string $token): array
    {
        try {
            return (array) JWT::decode($token, $this->publicKeyContent, explode(',', $this->algorithm));
        } catch (Exception $e) {
            throw new TokenDecodeException();
        }
    }
}
