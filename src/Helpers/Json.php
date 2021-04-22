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

namespace ISAAC\GazeHub\Helpers;

use JsonException;

use function json_encode;

use const JSON_THROW_ON_ERROR;

class Json
{
    /**
     * @param mixed $obj
     * @param string $default
     * @return string
     */
    public static function encode($obj, string $default): string
    {
        try {
            return json_encode($obj, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return $default;
        }
    }
}
