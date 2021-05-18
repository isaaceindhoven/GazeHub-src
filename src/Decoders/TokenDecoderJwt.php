<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Decoders;

use Exception;
use Firebase\JWT\JWT;
use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
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

    /**
     * TokenDecoderJwt constructor.
     * @param ConfigRepository $configRepository
     * @throws ConfigKeyNotFoundException
     */
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
