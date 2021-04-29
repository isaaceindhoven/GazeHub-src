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

use function json_encode;
use function preg_replace;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

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
            'You can change the configuration by adding a file called \'gazehub.config.json\' in your ' . "\n" .
            'current working directory, or give a path to the config file with the -c argument.' . "\n\n"
        );

        echo 'Config key you can use in gazehub.config.json, with their default values:' . "\n";

        $config = json_encode(self::$config, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        echo preg_replace(
            '/("\w+"):/',
            self::cEcho('01', '35', '${1}') . ':',
            $config
        );

        echo "\n";

        echo ("\nYou can also override config with environment variables, like this: `GAZEHUB_PORT=8000 ./gazehub`\n");
    }

    private static function cEcho(string $style, string $color, string $text): string
    {
        return "\033[" . $style . ';' . $color . 'm' . $text . "\033[0m";
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
