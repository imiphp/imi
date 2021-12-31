<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;
use Imi\Fpm\FpmApp;

require_once \dirname(__DIR__, 3) . '/vendor/autoload.php';

App::set(AppContexts::APP_PATH, \dirname(__DIR__), true);
App::run('Imi\Fpm\Test\Web', FpmApp::class);
