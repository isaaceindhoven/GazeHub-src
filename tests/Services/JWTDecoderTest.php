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

namespace GazeHub\Tests\Services;

use Exception;
use Firebase\JWT\JWT;
use GazeHub\Exceptions\JwtDecodeException;
use GazeHub\Services\ConfigRepository;
use GazeHub\Services\JWTDecoder;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function file_get_contents;
use function json_encode;
use function PHPUnit\Framework\assertEquals;
use function putenv;
use function sprintf;

class JWTDecoderTest extends TestCase
{
    /**
     * @var ConfigRepository
     */
    private $configRepo;

    public function __construct()
    {
        parent::__construct();
        $this->configRepo = new ConfigRepository();
        $this->configRepo->loadConfig(__DIR__ . '/../assets/testConfig.php');
    }

    /**
     * @return mixed[]
     */
    private function decode(bool $shouldVerify, bool $throwsException, string $token): array
    {
        if ($throwsException) {
            $this->expectException(JwtDecodeException::class);
        }
        putenv('GAZEHUB_JWT_VERIFY=' . ($shouldVerify ? '1' : '0'));
        $this->configRepo->loadConfig(__DIR__ . '/../assets/testConfig.php');
        $jwtDecoder = new JWTDecoder($this->configRepo);
        return $jwtDecoder->decode($token);
    }

    /**
     * @param mixed[] $payload
     * @return string
     */
    private function encode(array $payload): string
    {
        $privateKeyLoc = __DIR__ . '/../assets/private.test-key';
        $privateKeyContents = file_get_contents($privateKeyLoc);
        if ($privateKeyContents === false) {
            throw new Exception(sprintf('Private key %s could not be found', $privateKeyLoc));
        }
        return JWT::encode($payload, $privateKeyContents, $this->configRepo->get('jwt_alg'));
    }

    public function testIfJwtDecodeThrowsIfNot3Parts(): void
    {
        $this->decode(false, true, 'Kevin');
    }

    public function testIfJwtDecodeThrowsIfNotValidJsonInTheMiddle(): void
    {
        $this->decode(false, true, 'X.X.X');
    }

    public function testIfJwtDecodeThrowsIfEmptyString(): void
    {
        $this->decode(false, true, '');
    }

    public function testIfJwtDecodeThrowsIf3EmptyDots(): void
    {
        $this->decode(false, true, '...');
    }

    public function testIfJwtDecodeThrowsIfMiddleNotValidBase64(): void
    {
        $this->decode(false, true, 'X.~.X');
    }

    public function testIfDecodeReturnsPayload(): void
    {
        $payload = ['Kevin' => 1];
        // @phpstan-ignore-next-line
        $decoded = $this->decode(false, false, 'X.' . base64_encode(json_encode($payload)) . '.X');
        assertEquals($payload, $decoded);
    }

    public function testIfDecodeWorksWithPrivateKey(): void
    {
        $payload = ['Kevin' => 1];
        $decoded = $this->decode(true, false, $this->encode($payload));
        assertEquals($payload, $decoded);
    }
}
