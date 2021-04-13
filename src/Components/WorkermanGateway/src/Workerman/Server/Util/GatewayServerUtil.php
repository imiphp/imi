<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Util;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\ServerManager;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Contract\IWorkermanServerUtil;

/**
 * @Bean("GatewayServerUtil")
 */
class GatewayServerUtil implements IWorkermanServerUtil
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
        if (null === $clientId)
        {
            $clientId = ConnectContext::getClientId();
        }
        Gateway::sendToAll($data, (array) $clientId, null);

        return 1;
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
        if (null === $flag)
        {
            return $this->sendRaw($data, null, $serverName, $toAllWorkers);
        }
        Gateway::sendToUid($flag, $data);

        return 1;
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
        Gateway::sendToAll($data, null, null);

        return 1;
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

        return $this->sendRawToGroup($groupName, $dataParser->encode($data, $serverName), $server->getName(), $toAllWorkers);
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
        Gateway::sendToGroup($groupName, $data, null);

        return 1;
    }

    /**
     * 关闭一个或多个连接.
     *
     * @param int|int[]|string|string[]|null $clientId
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function close($clientId, ?string $serverName = null, bool $toAllWorkers = true): int
    {
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
            Gateway::closeClient($currentClientId);
            ++$count;
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

        $count = 0;
        foreach ((array) $flag as $tmpFlag)
        {
            foreach (Gateway::getClientIdByUid($tmpFlag) as $clientId)
            {
                Gateway::closeClient($clientId);
            }
            ++$count;
        }

        return $count;
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
