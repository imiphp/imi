<?php

declare(strict_types=1);

namespace Imi\Fpm\Server\Http\Listener;

use Imi\App;
use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Fpm\FpmAppContexts;
use Imi\Fpm\Server\Type;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\ServerManager;

class LoadRuntimeListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['route'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['route'] ?? [];
        if (isset($data['rules']))
        {
            $server = ServerManager::createServer('main', [
                'type'      => Type::HTTP,
                'namespace' => App::getNamespace(),
            ]);
            /** @var HttpRoute $route */
            $route = $server->getBean('HttpRoute');
            $route->setRules($data['rules']);
            App::set(FpmAppContexts::ROUTE_INITED, true);
        }
    }
}
