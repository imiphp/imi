<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\RequestContext;
use Imi\Server\Event\Listener\IAppInitEventListener;
use Imi\Server\Event\Param\AppInitEventParam;
use Imi\ServerManage;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.APP.INIT")
 */
class AppInit implements IAppInitEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(AppInitEventParam $e)
    {
        foreach (ServerManage::getServers() as $server)
        {
            if ($server->isLongConnection())
            {
                RequestContext::set('server', $server);
                $server->getBean('ConnectContextStore')->getHandler();
                if (Imi::getClassPropertyValue('ServerGroup', 'status'))
                {
                    /** @var \Imi\Server\Group\Handler\IGroupHandler $groupHandler */
                    $groupHandler = $server->getBean(Imi::getClassPropertyValue('ServerGroup', 'groupHandler'));
                    $groupHandler->clear();
                }
                App::getBean('ConnectionBinder');
            }
        }
    }
}
