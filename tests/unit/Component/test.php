<?php

declare(strict_types=1);

use Imi\App;
use Imi\Cli\CliApp;

require \dirname(__DIR__, 3) . '/vendor/autoload.php';

App::runApp(__DIR__, CliApp::class, function () {
    echo 'Test quick start', \PHP_EOL;
});
