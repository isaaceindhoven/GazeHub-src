<?php

declare(strict_types=1);

use ISAAC\GazeHub\Helpers\HelpPrinter;
use ISAAC\GazeHub\Hub;

// Load composer autoload
$composerFile = null;

$libraryAutoload = __DIR__ . '/../../../autoload.php';
$projectAutoload = __DIR__ . '/../vendor/autoload.php';

foreach ([$libraryAutoload, $projectAutoload] as $file) {
    if (file_exists($file)) {
        $composerFile = $file;
        break;
    }
}

if ($composerFile === null) {
    fwrite(STDERR, 'You can use GazeHub as dependency in a project, ' .
        'or as a standalone application, but make sure you install the dependencies using composer.');

    die(1);
}

require $composerFile;

$options = getopt('h');

if (array_key_exists('h', $options)) {
    HelpPrinter::print();
} else {
    try {
        $hub = new Hub(require(__DIR__ . '/../config/providers.php'));
        $hub->run();
    } catch (Exception $e) {
        fwrite(STDERR, 'Something went wrong while booting GazeHub.' . "\n" . $e->getMessage() . "\n");
        exit(1);
    }
}
