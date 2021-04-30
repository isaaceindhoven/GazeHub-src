<?php

declare(strict_types=1);

/*
 * Command for generating archive (PHAR):
 * php --define phar.readonly=0 create-archive.php
 */

try {
    $pharFile = 'gazehub.phar';

    if (file_exists($pharFile)) {
        unlink($pharFile);
    }

    $phar = new Phar($pharFile);

    $phar->startBuffering();

    $stub = $phar->createDefaultStub('bin/bootstrap.php');
    $stub = "#!/usr/bin/env php \n" . $stub;

    $phar->buildFromDirectory(__DIR__, '/\.(php|html)$/');
    $phar->setStub($stub);
    $phar->stopBuffering();
    $phar->compressFiles(Phar::GZ);

    chmod(__DIR__ . '/' . $pharFile, 0770);

    echo sprintf("%s successfully created\n", $pharFile);
} catch (Exception $e) {
    echo $e->getMessage();
}
