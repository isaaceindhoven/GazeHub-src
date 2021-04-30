<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Exceptions;

use Exception;

class DataValidationFailedException extends Exception
{
    /**
     * @var string[]
     */
    public $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct();
        $this->errors = $errors;
    }
}
