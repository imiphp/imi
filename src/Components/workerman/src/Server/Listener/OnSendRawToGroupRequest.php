<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Server\Event\PipeMessageEvent;
use Imi\Workerman\Server\Util\LocalServerUtil;

/**
 * 发送给分组中的连接-请求
 */
#[Listener(eventName: 'imi.pipe_message.sendRawToGroupRequest')]
class OnSendRawToGroupRequest implements IEventListener
{
    /**
     * @param PipeMessageEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $data = $e->data;
        ['data' => $data, 'groupName' => $groupName, 'serverName' => $serverName] = $data['data'];

        /** @var LocalServerUtil $serverUtil */
        $serverUtil = App::getBean(LocalServerUtil::class);
        $serverUtil->sendRawToGroup($groupName, $data, $serverName, false);
    }
}
