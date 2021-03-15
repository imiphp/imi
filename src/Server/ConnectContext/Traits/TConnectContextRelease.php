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
    public function release(int $fd): void
    {
        $groups = ConnectContext::get('__groups', [], $fd);

        // 当前连接离开所有组
        RequestContext::getServerBean('FdMap')->leaveAll($fd);

        ConnectContext::set('__groups', $groups, $fd);

        // 标记绑定连接释放
        if ($flag = ConnectContext::getFlagByFd($fd))
        {
            /** @var \Imi\Server\ConnectContext\StoreHandler $store */
            $store = RequestContext::getServerBean('ConnectContextStore');
            ConnectContext::unbind($flag, $store->getTtl());
        }

        ConnectContext::destroy($fd);
    }
}
