<?php

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
