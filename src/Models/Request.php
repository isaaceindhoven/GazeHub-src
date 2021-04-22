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

namespace ISAAC\GazeHub\Models;

use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;

use function array_key_exists;
use function str_replace;
use function trim;
use function urldecode;

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

    public function isAuthorized(): void
    {
        $token = $this->getTokenFromHeaderOrQuery();
        $this->tokenPayload = $this->tokenDecoder->decode($token);
    }

    /**
     * @param string $role
     * @throws UnauthorizedException
     */
    public function isRole(string $role): void
    {
        $this->isAuthorized();

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
    private function getQueryParam(string $key)
    {
        if (array_key_exists($key, $this->originalRequest->getQueryParams())) {
            $value = $this->originalRequest->getQueryParams()[$key];
            return urldecode($value);
        }
        return null;
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

    private function getTokenFromHeaderOrQuery(): string
    {
        $token = $this->getHeaderValueByKey('Authorization');

        if ($token === null) {
            $token = $this->getQueryParam('token');
        }

        if ($token === null) {
            $token = '';
        }

        return str_replace('Bearer ', '', $token);
    }
}
