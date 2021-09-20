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
 * 发送给标记对应的连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawByFlagRequest")
 */
class OnSendRawByFlagRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        ['data' => $data, 'flag' => $flag, 'serverName' => $serverName] = $data['data'];
        RequestContext::set('server', ServerManager::getServer($serverName));

        /** @var LocalServerUtil $serverUtil */
        $serverUtil = App::getBean(LocalServerUtil::class);
        $serverUtil->sendRawByFlag($data, $flag, $serverName, false);
    }
}
