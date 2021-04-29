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

use ISAAC\GazeHub\Decoders\TokenDecoderJwt;

return [
    'port' => 3333,
    'host' => '0.0.0.0',
    'enable_debug_page' => false,
    'token_decoder' => TokenDecoderJwt::class,
    'jwt_public_key' => '',
    'jwt_alg' => 'RS256',
    'log_level' => 'INFO', // ['DEBUG', 'INFO', 'ERROR']
];
