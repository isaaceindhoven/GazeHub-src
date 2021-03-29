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

namespace GazeHub\Models;

use GazeHub\Exceptions\DataValidationFailedException;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Services\JWTDecoder;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;
use Throwable;

use function array_key_exists;
use function str_replace;
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
     * @var JWTDecoder;
     */
    private $jwtDecoder;

    public function __construct(JWTDecoder $jwtDecoder)
    {
        $this->jwtDecoder = $jwtDecoder;
    }

    public function setOriginalRequest(ServerRequestInterface $request): void
    {
        $this->originalRequest = $request;
    }

    public function isAuthorized(): void
    {
        $token = $this->getHeaderValueByKey('Authorization');


        if ($token === null) {
            $token = $this->getQueryParam('token');
        }

        if ($token === null) {
            throw new UnAuthorizedException();
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            $this->tokenPayload = $this->jwtDecoder->decode($token);
        } catch (Throwable $th) {
            throw new UnAuthorizedException();
        }
    }

    public function isRole(string $role): void
    {
        $this->isAuthorized();

        if ($this->getTokenPayload()['role'] !== $role) {
            throw new UnAuthorizedException();
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
     * @return null|string
     */
    private function getHeaderValueByKey(string $key)
    {
        $value = $this->originalRequest->getHeaderLine($key);
        if ($value === '') {
            return null;
        }
        return $value;
    }
}
