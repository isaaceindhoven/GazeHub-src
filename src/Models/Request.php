<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Models;

use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;

use function str_replace;
use function trim;

class Request
{
    /**
     * @var mixed[]
     */
    private $tokenPayload;

    /**
     * @var ServerRequestInterface;
     */
    private $originalRequest;

    /**
     * @var TokenDecoder;
     */
    private $tokenDecoder;

    public function __construct(TokenDecoder $tokenDecoder, ServerRequestInterface $request)
    {
        $this->tokenDecoder = $tokenDecoder;
        $this->originalRequest = $request;
    }

    /**
     * @throws UnauthorizedException
     */
    public function isAuthorized(): void
    {
        $token = $this->getAuthTokenFromHeader();

        if ($token === '') {
            throw new UnauthorizedException();
        }
    }

    /**
     * @param string $role
     * @throws UnauthorizedException
     * @throws TokenDecodeException
     */
    public function isRole(string $role): void
    {
        $this->isAuthorized();
        $this->tokenPayload = $this->tokenDecoder->decode($this->getAuthTokenFromHeader());

        if ($this->getTokenPayload()['role'] !== $role) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @return mixed[]
     */
    public function getBody(): array
    {
        return (array) $this->originalRequest->getParsedBody();
    }

    /**
     * @return mixed[]
     */
    public function getTokenPayload(): array
    {
        return $this->tokenPayload;
    }

    /**
     * @param string[] $checks
     * @return mixed[]
     * @throws DataValidationFailedException
     */
    public function validate(array $checks): array
    {
        $validator = new Validator();

        $validation = $validator->validate($this->getBody(), $checks);

        if ($validation->fails()) {
            throw new DataValidationFailedException($validation->errors()->toArray());
        }

        return $validation->getValidData();
    }

    /**
     * @param string $key
     * @return null|string
     */
    private function getHeaderValueByKey(string $key)
    {
        $value = $this->originalRequest->getHeaderLine($key);
        if (trim($value) === '') {
            return null;
        }
        return $value;
    }

    public function getAuthTokenFromHeader(): string
    {
        $token = $this->getHeaderValueByKey('Authorization');

        if ($token === null) {
            $token = '';
        }

        return str_replace('Bearer ', '', $token);
    }

    public function getIp(): string {
        return $this->originalRequest->getServerParams()["REMOTE_ADDR"];
    }
}
