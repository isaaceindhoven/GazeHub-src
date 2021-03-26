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

namespace GazeHub\Services;

use Firebase\JWT\JWT;

use function base64_decode;
use function explode;
use function file_get_contents;
use function json_decode;

class JWTDecoder
{
    /**
     * @var string
     */
    private $publicKeyContent;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var bool
     */
    private $jwtVerify;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->jwtVerify = (bool) $configRepository->get('jwt_verify');
        if ($this->jwtVerify) {
            $publicKeyContent = file_get_contents($configRepository->get('jwt_public_key'));
            $this->publicKeyContent = $publicKeyContent === false ? '' : $publicKeyContent;
        }
        $this->algorithm = $configRepository->get('jwt_alg');
    }

    /**
     * @param string $token
     * @return mixed[]
     */
    public function decode(string $token): array
    {
        if ($this->jwtVerify) {
            return (array) JWT::decode($token, $this->publicKeyContent, explode(',', $this->algorithm));
        } else {
            $base64Decoded = base64_decode(explode('.', $token)[1], true);
            return json_decode($base64Decoded === false ? '' : $base64Decoded, true);
        }
    }
}
