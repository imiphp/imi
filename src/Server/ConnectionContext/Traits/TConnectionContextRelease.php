<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\Traits;

use Imi\ConnectionContext;
use Imi\RequestContext;

/**
 * 连接上下文关闭释放.
 */
trait TConnectionContextRelease
{
    /**
     * @param int|string $clientId
     */
    public function release($clientId): void
    {
        $groups = ConnectionContext::get('__groups', [], $clientId);

        $server = RequestContext::getServer();
        // 当前连接离开所有组
        $server->getBean('ClientIdMap')->leaveAll($clientId);

        ConnectionContext::set('__groups', $groups, $clientId);

        // 标记绑定连接释放
        if ($flag = ConnectionContext::getFlagByClientId($clientId))
        {
            /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
            $store = $server->getBean('ConnectionContextStore');
            ConnectionContext::unbind($flag, $clientId, $store->getTtl());
        }

        ConnectionContext::destroy($clientId);
    }
}
