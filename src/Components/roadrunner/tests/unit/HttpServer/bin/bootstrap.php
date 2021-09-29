<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;
use Imi\RoadRunner\RoadRunnerApp;

require_once dirname(__DIR__, 7) . '/vendor/' . 'autoload.php';
require_once dirname(__DIR__, 4) . '/vendor/' . 'autoload.php';

App::set(AppContexts::APP_PATH, dirname(__DIR__), true);
App::run('Imi\RoadRunner\Test\HttpServer', RoadRunnerApp::class);
