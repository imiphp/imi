<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\Traits;

use Imi\ConnectContext;
use Imi\RequestContext;

/**
 * 连接上下文关闭释放.
 */
trait TConnectContextRelease
{
    /**
     * @param int|string $clientId
     */
    public function release($clientId): void
    {
        $groups = ConnectContext::get('__groups', [], $clientId);

        // 当前连接离开所有组
        RequestContext::getServerBean('ClientIdMap')->leaveAll($clientId);

        ConnectContext::set('__groups', $groups, $clientId);

        // 标记绑定连接释放
        if ($flag = ConnectContext::getFlagByClientId($clientId))
        {
            /** @var \Imi\Server\ConnectContext\StoreHandler $store */
            $store = RequestContext::getServerBean('ConnectContextStore');
            ConnectContext::unbind($flag, $clientId, $store->getTtl());
        }

        ConnectContext::destroy($clientId);
    }
}
