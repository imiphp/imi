<?php

declare(strict_types=1);

namespace Imi\Fpm\Server\Http;

use Imi\Event\EventParam;
use Imi\Server\Http\Listener\HttpRouteInit;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\ServerManager;

class FpmHttpRouteInit
{
    public function init()
    {
        $server = ServerManager::getServer('main');
        /** @var HttpRoute $route */
        $route = $server->getBean('HttpRoute');
        if (!$route->loadCache())
        {
            (new HttpRouteInit())->handle(new EventParam(''));
        }
    }
}
