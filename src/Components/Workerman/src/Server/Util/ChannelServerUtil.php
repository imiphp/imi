<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Channel\Client;
use Imi\ConnectContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Worker;

class ChannelServerUtil extends LocalServerUtil
{
    /**
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessage(string $action, array $data = [], $workerId = null, ?string $serverName = null): int
    {
        $data['action'] = $action;

        return $this->sendMessageRaw($data, $workerId, $serverName);
    }

    /**
     * 发送消息给 Worker 进程.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessageRaw(array $data, $workerId = null, ?string $serverName = null): int
    {
        if (null === $workerId)
        {
            $workerId = range(0, Worker::getWorkerNum() - 1);
        }
        $eventName = 'imi.process.message.' . (null === $serverName ? ($serverName . '.') : '');
        $success = 0;
        $currentWorkerId = Worker::getWorkerId();
        $currentServerName = RequestContext::getServer()->getName();
        foreach ((array) $workerId as $tmpWorkerId)
        {
            if ($tmpWorkerId === $currentWorkerId && (null === $serverName || $currentServerName === $serverName))
            {
                $action = $data['action'] ?? null;
                if (!$action)
                {
                    continue;
                }
                Event::trigger('IMI.PIPE_MESSAGE.' . $action, [
                    'data'      => $data,
                ]);
                ++$success;
            }
            else
            {
                Client::publish($eventName . $tmpWorkerId, $data);
                ++$success;
            }
        }

        return $success;
    }

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
            $fd = ConnectContext::getFd();
            if (!$fd)
            {
                return 0;
            }
            $fds = [$fd];

            return $this->sendRaw($data, $fds, $serverName);
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
