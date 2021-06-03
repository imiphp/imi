<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Workerman\Server\Util\LocalServerUtil;

/**
 * 发送给所有连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawToAllRequest")
 */
class OnSendRawToAllRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        ['data' => $data, 'serverName' => $serverName] = $data['data'];

        /** @var LocalServerUtil $serverUtil */
        $serverUtil = App::getBean(LocalServerUtil::class);
        $serverUtil->sendRawToAll($data, $serverName, false);
    }
}
