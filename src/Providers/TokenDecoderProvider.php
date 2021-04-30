<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Decoders\TokenDecoderJwt;
use ISAAC\GazeHub\Decoders\TokenDecoderJwtPlacebo;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use Psr\Log\LoggerInterface;

class TokenDecoderProvider implements Provider
{
    public function register(Container &$container): void
    {
        $configRepo = $container->get(ConfigRepository::class);

        $publicKey = $configRepo->get('jwt_public_key');

        if ($publicKey === '') {
            /** @var LoggerInterface */
            $logger = $container->get(LoggerInterface::class);
            $logger->warning('No public key provided. Token decode will not validate JWT. DONT USE IN PRODUCTION');
            $container->set(TokenDecoder::class, $container->get(TokenDecoderJwtPlacebo::class));
        } else {
            $container->set(TokenDecoder::class, $container->get(TokenDecoderJwt::class));
        }
    }
}
