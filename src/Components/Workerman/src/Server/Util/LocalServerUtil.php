<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Channel\Client;
use Imi\ConnectContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\ServerManager;
use Imi\Worker;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Contract\IWorkermanServerUtil;
use Workerman\Connection\TcpConnection;

class LocalServerUtil implements IWorkermanServerUtil
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
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed          $data
     * @param int|int[]|null $fd           为 null 时，则发送给当前连接
     * @param string|null    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function send($data, $fd = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRaw($dataParser->encode($data, $serverName), $fd, $server->getName(), $toAllWorkers);
    }

    /**
     * 发送数据给指定标记的客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed                $data
     * @param string|string[]|null $flag         为 null 时，则发送给当前连接
     * @param string|null          $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendByFlag($data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $flag)
        {
            $fd = ConnectContext::getFd();
            if (!$fd)
            {
                return 0;
            }
            $fds = [$fd];

            return $this->send($data, $fds, $serverName);
        }
        else
        {
            /** @var \Imi\Server\DataParser\DataParser $dataParser */
            $dataParser = $server->getBean(DataParser::class);

            return $this->sendRawByFlag($dataParser->encode($data, $serverName), $flag, $serverName, $toAllWorkers);
        }
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * @param int|int[]|null $fd           为 null 时，则发送给当前连接
     * @param string|null    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendRaw(string $data, $fd = null, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        $worker = $server->getWorker();
        if (null === $fd)
        {
            $fd = ConnectContext::getFd();
            if (!$fd)
            {
                return 0;
            }
        }
        $success = 0;
        foreach ((array) $fd as $tmpFd)
        {
            /** @var TcpConnection|null $connection */
            $connection = $worker->connections[$tmpFd] ?? null;
            if (null !== $connection && $connection->send($data))
            {
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
     * 数据将会通过处理器编码
     *
     * @param mixed       $data
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendToAll($data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawToAll($dataParser->encode($data, $serverName), $server->getName(), $toAllWorkers);
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
     * 数据将会通过处理器编码
     *
     * @param string|string[] $groupName
     * @param mixed           $data
     * @param string|null     $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool            $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendToGroup($groupName, $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawToGroup($groupName, $dataParser->encode($data, $serverName), $server->getName());
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
     * 关闭一个或多个连接.
     *
     * @param int|int[]|null $fd
     * @param bool           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function close($fd, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server)
        {
            return 0;
        }
        $worker = $server->getWorker();
        $count = 0;
        foreach ((array) $fd as $currentFd)
        {
            /** @var TcpConnection|null $connection */
            $connection = $worker->connections[$currentFd] ?? null;
            if ($connection)
            {
                $connection->close();
                ++$count;
            }
        }

        return $count;
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

    /**
     * 获取服务器.
     */
    public function getServer(?string $serverName = null): ?IWorkermanServer
    {
        if (null === $serverName)
        {
            $server = RequestContext::getServer();
            if ($server)
            {
                // @phpstan-ignore-next-line
                return $server;
            }
            $serverName = 'main';
        }

        // @phpstan-ignore-next-line
        return ServerManager::getServer($serverName, IWorkermanServer::class);
    }
}
