<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Workerman\Server\Util\LocalServerUtil;

/**
 * 发送给分组中的连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawToGroupRequest")
 */
class OnSendRawToGroupRequest implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        ['data' => $data, 'groupName' => $groupName, 'serverName' => $serverName] = $data['data'];
        RequestContext::set('server', ServerManager::getServer($serverName));

        /** @var LocalServerUtil $serverUtil */
        $serverUtil = App::getBean(LocalServerUtil::class);
        $serverUtil->sendRawToGroup($groupName, $data, $serverName, false);
    }
}
