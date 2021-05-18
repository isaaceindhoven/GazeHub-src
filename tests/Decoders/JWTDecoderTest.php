<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Decoders;

use Exception;
use Firebase\JWT\JWT;
use ISAAC\GazeHub\Decoders\TokenDecoderJwt;
use ISAAC\GazeHub\Exceptions\ConfigKeyNotFoundException;
use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use ISAAC\GazeHub\Repositories\ConfigRepository;
use ISAAC\GazeHub\Tests\BaseTest;

use function file_get_contents;
use function PHPUnit\Framework\assertEquals;
use function sprintf;

class JWTDecoderTest extends BaseTest
{
    /**
     * @var ConfigRepository
     */
    private $configRepo;

    public function __construct()
    {
        parent::__construct();
        $this->configRepo = $this->container->get(ConfigRepository::class);
    }

    /**
     * @param bool $throwsException
     * @param string $token
     * @return mixed[]
     * @throws TokenDecodeException
     * @throws ConfigKeyNotFoundException
     */
    private function decode(bool $throwsException, string $token): array
    {
        if ($throwsException) {
            $this->expectException(TokenDecodeException::class);
        }
        $jwtDecoder = new TokenDecoderJwt($this->configRepo);
        return $jwtDecoder->decode($token);
    }

    /**
     * @param mixed[] $payload
     * @return string
     * @throws ConfigKeyNotFoundException
     * @throws Exception
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

    /**
     * @throws TokenDecodeException
     * @throws ConfigKeyNotFoundException
     */
    public function testIfJwtDecodeThrowsIfNot3Parts(): void
    {
        $this->decode(true, 'Kevin');
    }

    /**
     * @throws TokenDecodeException
     * @throws ConfigKeyNotFoundException
     */
    public function testIfJwtDecodeThrowsIfNotValidJsonInTheMiddle(): void
    {
        $this->decode(true, 'X.X.X');
    }

    /**
     * @throws TokenDecodeException
     * @throws ConfigKeyNotFoundException
     */
    public function testIfJwtDecodeThrowsIfEmptyString(): void
    {
        $this->decode(true, '');
    }

    /**
     * @throws TokenDecodeException
     * @throws ConfigKeyNotFoundException
     */
    public function testIfJwtDecodeThrowsIf3EmptyDots(): void
    {
        $this->decode(true, '...');
    }

    /**
     * @throws TokenDecodeException
     * @throws ConfigKeyNotFoundException
     */
    public function testIfJwtDecodeThrowsIfMiddleNotValidBase64(): void
    {
        $this->decode(true, 'X.~.X');
    }

    /**
     * @throws ConfigKeyNotFoundException
     * @throws TokenDecodeException
     */
    public function testIfDecodeWorksWithPrivateKey(): void
    {
        $payload = ['Kevin' => 1];
        $decoded = $this->decode(false, $this->encode($payload));
        assertEquals($payload, $decoded);
    }
}
