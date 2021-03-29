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

namespace GazeHub;

use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;

use function array_map;
use function date;
use function implode;
use function intval;
use function is_string;
use function json_encode;
use function sprintf;

use const STDOUT;

class Log
{
    public const ERROR = 1;
    public const WARN = 2;
    public const INFO = 3;
    public const DEBUG = 4;

    /**
     * @var int
     */
    private static $logLevel = 0;

    /**
     * @var WritableResourceStream
     */
    private static $stream;

    public static function setLogLevel(LoopInterface $loop, string $logLevel): void
    {
        self::$logLevel = intval($logLevel);
        self::$stream = new WritableResourceStream(STDOUT, $loop);
    }

    /**
     * @param string $code
     * @param mixed[] $args
     * @return void
     */
    private static function printMsg(string $code, array $args): void
    {
        $args = array_map(static function ($x): string {
            if (!is_string($x)) {
                return json_encode($x) === false ? '' : json_encode($x);
            }
            return $x;
        }, $args);

        self::$stream->write(
            sprintf("[%s] \033[%sm%s \033[0m\n", date('c'), $code, implode(' ', $args))
        );
    }

    /**
     * @param mixed $args
     */
    public static function error(...$args): void
    {
        if (self::$logLevel >= self::ERROR) {
            Log::printMsg('31', $args);
        }
    }

    /**
     * @param mixed $args
     */
    public static function info(...$args): void
    {
        if (self::$logLevel >= self::INFO) {
            Log::printMsg('32', $args);
        }
    }

    /**
     * @param mixed $args
     */
    public static function debug(...$args): void
    {
        if (self::$logLevel >= self::DEBUG) {
            Log::printMsg('36', $args);
        }
    }
}
