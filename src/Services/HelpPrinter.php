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

namespace ISAAC\GazeHub\Services;

use function array_keys;
use function array_map;
use function intval;
use function max;
use function str_repeat;
use function strlen;
use function strtoupper;

class HelpPrinter
{
    /**
     * @var string[]
     */
    private static $config;

    public static function print(): void
    {
        self::$config = include(__DIR__ . '/../../config/config.php');

        echo self::cEcho(
            '01',
            '30',
            self::getAsciiLogo() .
            "Below you'll find the environment variables with their default values.\n"
        );

        echo ("To change the port for example you could run `GAZEHUB_SERVER_PORT=8000 ./gazehub`\n\n");

        $longestKey = self::getLongestKeyLength();

        foreach (self::$config as $key => $default) {
            echo(
                self::cEcho('01', '35', 'GAZEHUB_' . strtoupper($key)) .
                str_repeat(' ', $longestKey - strlen($key)) .
                self::cEcho('00', '33', '  >  ' . $default) .
                "\n"
            );
        }

        echo "\n";
    }

    private static function cEcho(string $style, string $color, string $text): string
    {
        return "\033[" . $style . ';' . $color . 'm' . $text . "\033[0m";
    }

    private static function getLongestKeyLength(): int
    {
        return intval(
            max(array_map(static function ($x): int {
                return strlen($x);
            }, array_keys(self::$config)))
        );
    }

    private static function getAsciiLogo(): string
    {
        return (
            "\n\n" .
            "   _____               _    _       _     \n" .
            "  / ____|             | |  | |     | |    \n" .
            " | |  __  __ _ _______| |__| |_   _| |__  \n" .
            " | | |_ |/ _` |_  / _ \  __  | | | | '_ \ \n" .
            " | |__| | (_| |/ /  __/ |  | | |_| | |_) |\n" .
            "  \_____|\__,_/___\___|_|  |_|\__,_|_.__/ \n" .
            "\n\n"
        );
    }
}
