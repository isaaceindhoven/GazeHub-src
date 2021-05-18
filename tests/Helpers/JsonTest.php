<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Tests\Helpers;

use ISAAC\GazeHub\Helpers\Json;
use ISAAC\GazeHub\Tests\BaseTest;

use function fopen;

class JsonTest extends BaseTest
{
    public function testJsonEncodeReturnsDefaultOnError(): void
    {
        $default = 'Test';

        $result = Json::encode(fopen('/dev/null', 'r'), $default);

        self::assertEquals($default, $result);
    }
}
