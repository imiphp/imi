<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Imi\ConnectContext;

abstract class AbstractDistributedServerUtil extends LocalServerUtil
{
    /**
     * 发送数据给指定标记的客户端，支持一个或多个（数组）.
     *
     * @param string|string[]|null $flag         为 null 时，则发送给当前连接
     * @param string|null          $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendRawByFlag(string $data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }
        if (null === $flag)
        {
            $clientId = ConnectContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [$clientId];

            return $this->sendRaw($data, $clientIds, $serverName);
        }
        else
        {
            return (int) ($this->sendMessage('sendRawByFlagRequest', [
                'data'         => $data,
                'flag'         => $flag,
                'serverName'   => $serverName,
                'toAllWorkers' => $toAllWorkers,
            ], null, $serverName) > 0);
        }
    }

    /**
     * 发送数据给所有客户端.
     *
     * 数据原样发送
     *
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }

        return (int) ($this->sendMessage('sendRawToAllRequest', [
            'data'         => $data,
            'serverName'   => $serverName,
            'toAllWorkers' => $toAllWorkers,
        ], null, $serverName) > 0);
    }

    /**
     * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
     *
     * 数据原样发送
     *
     * @param string|string[] $groupName
     * @param string|null     $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool            $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendRawToGroup($groupName, string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }

        return (int) ($this->sendMessage('sendRawToGroupRequest', [
            'groupName'    => $groupName,
            'data'         => $data,
            'serverName'   => $serverName,
            'toAllWorkers' => $toAllWorkers,
        ], null, $serverName) > 0);
    }

    /**
     * 关闭一个或多个指定标记的连接.
     *
     * @param string|string[]|null $flag
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        if (null === $flag)
        {
            return $this->close(null, $serverName, $toAllWorkers);
        }
        if (null === $serverName)
        {
            $server = $this->getServer($serverName);
            if (!$server)
            {
                return 0;
            }
            $serverName = $server->getName();
        }

        return (int) ($this->sendMessage('closeByFlagRequest', [
            'flag'         => $flag,
            'serverName'   => $serverName,
            'toAllWorkers' => $toAllWorkers,
        ], null, $serverName) > 0);
    }
}
