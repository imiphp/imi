<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\ConnectContext\ConnectionBinder;
use Imi\Server\DataParser\DataParser;
use Imi\Server\ServerManager;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Contract\IWorkermanServerUtil;
use Workerman\Connection\TcpConnection;

/**
 * @Bean("LocalServerUtil")
 */
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
        throw new \RuntimeException('Unsupport operation');
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
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed                          $data
     * @param int|int[]|string|string[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null                    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function send($data, $clientId = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRaw($dataParser->encode($data, $serverName), $clientId, $server->getName(), $toAllWorkers);
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

        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawByFlag($dataParser->encode($data, $serverName), $flag, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * @param int|int[]|string|string[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null                    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function sendRaw(string $data, $clientId = null, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        $worker = $server->getWorker();
        if (null === $clientId)
        {
            $clientId = ConnectContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
        }
        $success = 0;
        foreach ((array) $clientId as $tmpClientId)
        {
            /** @var TcpConnection|null $connection */
            $connection = $worker->connections[$tmpClientId] ?? null;
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
            $clientId = ConnectContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [$clientId];

            return $this->sendRaw($data, $clientIds, $serverName, false);
        }
        else
        {
            /** @var ConnectionBinder $connectionBinder */
            $connectionBinder = App::getBean('ConnectionBinder');
            $clientIds = [];
            foreach ((array) $flag as $tmpFlag)
            {
                $clientId = $connectionBinder->getClientIdByFlag($tmpFlag);
                if ($clientId)
                {
                    $clientIds = array_merge($clientIds, $clientId);
                }
            }
            if (!$clientIds)
            {
                return 0;
            }

            return $this->sendRaw($data, $clientIds, $serverName, false);
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

        return $this->sendRawToAll($dataParser->encode($data, $serverName), $server->getName(), false);
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

        $count = 0;
        /** @var TcpConnection $connection */
        foreach ($server->getWorker()->connections as $connection)
        {
            if ($connection->send($data))
            {
                ++$count;
            }
        }

        return $count;
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

        $count = 0;
        $groups = (array) $groupName;
        foreach ($groups as $tmpGroupName)
        {
            $group = $server->getGroup($tmpGroupName);
            if ($group)
            {
                $count += $this->sendRaw($data, $group->getClientIds(), $serverName, false);
            }
        }

        return $count;
    }

    /**
     * 关闭一个或多个连接.
     *
     * @param int|int[]|string|string[]|null $clientId
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function close($clientId, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server)
        {
            return 0;
        }
        $worker = $server->getWorker();
        $count = 0;
        if (null === $clientId)
        {
            $clientId = ConnectContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [(int) $clientId];
        }
        else
        {
            $clientIds = (array) $clientId;
        }
        foreach ($clientIds as $currentClientId)
        {
            /** @var TcpConnection|null $connection */
            $connection = $worker->connections[$currentClientId] ?? null;
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
            return $this->close(null, $serverName, false);
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

        /** @var ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $clientIds = [];
        foreach ((array) $flag as $tmpFlag)
        {
            $clientId = $connectionBinder->getClientIdByFlag($tmpFlag);
            if ($clientId)
            {
                $clientIds = array_merge($clientIds, $clientId);
            }
        }
        if (!$clientIds)
        {
            return 0;
        }

        return $this->close($clientIds, $serverName, false);
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
