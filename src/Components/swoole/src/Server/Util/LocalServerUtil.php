<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleServerUtil;
use Imi\Swoole\Server\Event\Param\PipeMessageEventParam;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Worker;

/**
 * @Bean("LocalServerUtil")
 */
class LocalServerUtil implements ISwooleServerUtil
{
    protected bool $needResponse = true;

    /**
     * 等待响应超时时间.
     */
    protected float $waitResponseTimeout = 30;

    /**
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessage(string $action, array $data = [], $workerId = null): int
    {
        $data['action'] = $action;
        $message = json_encode($data);

        return $this->sendMessageRaw($message, $workerId);
    }

    /**
     * 发送消息给 Worker 进程.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessageRaw(string $message, $workerId = null): int
    {
        if (null === $workerId)
        {
            $workerId = range(0, Worker::getWorkerNum() - 1);
        }
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);
        $swooleServer = $server->getSwooleServer();
        $success = 0;
        $currentWorkerId = Worker::getWorkerId();
        foreach ((array) $workerId as $tmpWorkerId)
        {
            if ($tmpWorkerId === $currentWorkerId)
            {
                go(function () use ($server, $currentWorkerId, $message) {
                    Event::trigger('IMI.MAIN_SERVER.PIPE_MESSAGE', [
                        'server'    => $server,
                        'workerId'  => $currentWorkerId,
                        'message'   => $message,
                    ], $server, PipeMessageEventParam::class);
                });
                ++$success;
            }
            elseif ($swooleServer->sendMessage($message, $tmpWorkerId))
            {
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
     * @param mixed                          $data
     * @param int|int[]|string|string[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null                    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public function send($data, $clientId = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }

        return $this->sendRaw($dataParser->encode($data, $serverName), $clientId, $serverName, $toAllWorkers);
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
        if (null === $flag)
        {
            $clientId = ConnectionContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [(int) $clientId];
        }
        else
        {
            $clientIds = [];
            foreach ((array) $flag as $tmpFlag)
            {
                $clientId = ConnectionContext::getClientIdByFlag($tmpFlag, $serverName);
                if ($clientId)
                {
                    $clientIds = array_merge($clientIds, $clientId);
                }
            }
            if (!$clientIds)
            {
                return 0;
            }
        }

        return $this->send($data, $clientIds, $serverName, $toAllWorkers);
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
        $swooleServer = $server->getSwooleServer();
        if (null === $clientId)
        {
            $clientId = ConnectionContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
        }
        $clientIds = (array) $clientId;
        $success = 0;
        if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $method = 'push';
        }
        else
        {
            $method = 'send';
        }
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && 'push' === $method)
        {
            $id = uniqid('', true);
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('sendToClientIdsRequest', [
                    'messageId'          => $id,
                    'clientIds'          => $clientIds,
                    'data'               => $data,
                    'serverName'         => $server->getName(),
                    'needResponse'       => $this->needResponse,
                ]);
                if (isset($channel) && ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
                {
                    for ($i = $count; $i > 0; --$i)
                    {
                        $result = $channel->pop($this->waitResponseTimeout);
                        if (false === $result)
                        {
                            break;
                        }
                        $success += ($result['result'] ?? 0);
                    }
                }
            }
            finally
            {
                if ($this->needResponse)
                {
                    ChannelContainer::removeChannel($id);
                }
            }
        }
        else
        {
            foreach ($clientIds as $tmpClientId)
            {
                $tmpClientId = (int) $tmpClientId;
                /** @var \Swoole\WebSocket\Server $swooleServer */
                if ('push' === $method && !$swooleServer->isEstablished($tmpClientId))
                {
                    continue;
                }
                if ($swooleServer->$method($tmpClientId, $data))
                {
                    ++$success;
                }
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
        if (null === $flag)
        {
            $clientId = ConnectionContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [(int) $clientId];
        }
        else
        {
            $clientIds = [];
            foreach ((array) $flag as $tmpFlag)
            {
                $clientId = ConnectionContext::getClientIdByFlag($tmpFlag, $serverName);
                if ($clientId)
                {
                    $clientIds = array_merge($clientIds, $clientId);
                }
            }
            if (!$clientIds)
            {
                return 0;
            }
        }

        return $this->sendRaw($data, $clientIds, $serverName, $toAllWorkers);
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
        $swooleServer = $server->getSwooleServer();
        $success = 0;
        if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $method = 'push';
        }
        else
        {
            $method = 'send';
        }
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && 'push' === $method)
        {
            $id = uniqid('', true);
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('sendRawToAllRequest', [
                    'messageId'     => $id,
                    'data'          => $data,
                    'serverName'    => $server->getName(),
                    'needResponse'  => $this->needResponse,
                ]);
                if (isset($channel) && ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
                {
                    for ($i = $count; $i > 0; --$i)
                    {
                        $result = $channel->pop($this->waitResponseTimeout);
                        if (false === $result)
                        {
                            break;
                        }
                        $success += ($result['result'] ?? 0);
                    }
                }
            }
            finally
            {
                if ($this->needResponse)
                {
                    ChannelContainer::removeChannel($id);
                }
            }
        }
        else
        {
            foreach ($server->getSwoolePort()->connections as $clientId)
            {
                /** @var \Swoole\WebSocket\Server $swooleServer */
                if ('push' === $method && !$swooleServer->isEstablished($clientId))
                {
                    continue;
                }
                if ($swooleServer->$method($clientId, $data))
                {
                    ++$success;
                }
            }
        }

        return $success;
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
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        $groups = (array) $groupName;
        $success = 0;
        if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $method = 'push';
        }
        else
        {
            $method = 'send';
        }
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && 'push' === $method)
        {
            $id = uniqid('', true);
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('sendToGroupsRequest', [
                    'messageId'     => $id,
                    'groups'        => $groups,
                    'data'          => $data,
                    'serverName'    => $server->getName(),
                    'needResponse'  => $this->needResponse,
                ]);
                if (isset($channel) && ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
                {
                    for ($i = $count; $i > 0; --$i)
                    {
                        $result = $channel->pop($this->waitResponseTimeout);
                        if (false === $result)
                        {
                            break;
                        }
                        $success += ($result['result'] ?? 0);
                    }
                }
            }
            finally
            {
                if ($this->needResponse)
                {
                    ChannelContainer::removeChannel($id);
                }
            }
        }
        else
        {
            foreach ($groups as $tmpGroupName)
            {
                $group = $server->getGroup($tmpGroupName);
                if ($group)
                {
                    $result = $group->$method($data);
                    foreach ($result as $item)
                    {
                        if ($item)
                        {
                            ++$success;
                        }
                    }
                }
            }
        }

        return $success;
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
        $swooleServer = $server->getSwooleServer();
        $count = 0;
        if (null === $clientId)
        {
            $clientId = ConnectionContext::getClientId();
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
            if ($swooleServer->close((int) $currentClientId))
            {
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
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        $result = 0;
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && null !== $flag)
        {
            $id = uniqid('', true);
            try
            {
                $channel = ChannelContainer::getChannel($id);
                $count = $this->sendMessage('closeByFlagRequest', [
                    'messageId'    => $id,
                    'flag'         => $flag,
                    'serverName'   => $server->getName(),
                    'needResponse' => true,
                ]);
                if (ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
                {
                    for ($i = $count; $i > 0; --$i)
                    {
                        $popResult = $channel->pop($this->waitResponseTimeout);
                        if (false === $popResult)
                        {
                            break;
                        }
                        $result += ($popResult['result'] ?? 0);
                    }
                }
            }
            finally
            {
                ChannelContainer::removeChannel($id);
            }
        }
        else
        {
            if (null === $flag)
            {
                $clientIds = [ConnectionContext::getClientId()];
            }
            else
            {
                $clientIds = ConnectionContext::getClientIdByFlag($flag, $serverName);
                if (!$clientIds)
                {
                    return 0;
                }
            }
            foreach ($clientIds as $clientId)
            {
                if ($swooleServer->close((int) $clientId))
                {
                    ++$result;
                }
            }
        }

        return $result;
    }

    /**
     * 连接是否存在.
     *
     * @param string|int|null $clientId
     */
    public function exists($clientId, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        if (null === $clientId)
        {
            $clientId = ConnectionContext::getClientId();
        }
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers)
        {
            $id = uniqid('', true);
            try
            {
                $channel = ChannelContainer::getChannel($id);
                $count = $this->sendMessage('existsRequest', [
                    'messageId'    => $id,
                    'clientId'     => $clientId,
                    'serverName'   => $server->getName(),
                    'needResponse' => true,
                ]);
                if (ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
                {
                    for ($i = $count; $i > 0; --$i)
                    {
                        $result = $channel->pop($this->waitResponseTimeout);
                        if (false === $result)
                        {
                            break;
                        }
                        if ($result['result'] ?? false)
                        {
                            return true;
                        }
                    }
                }
            }
            finally
            {
                ChannelContainer::removeChannel($id);
            }
        }
        else
        {
            return $swooleServer->exists((int) $clientId);
        }

        return false;
    }

    /**
     * 指定标记的连接是否存在.
     */
    public function flagExists(?string $flag, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && null !== $flag)
        {
            $id = uniqid('', true);
            try
            {
                $channel = ChannelContainer::getChannel($id);
                $count = $this->sendMessage('existsRequest', [
                    'messageId'    => $id,
                    'flag'         => $flag,
                    'serverName'   => $server->getName(),
                    'needResponse' => true,
                ]);
                if (ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
                {
                    for ($i = $count; $i > 0; --$i)
                    {
                        $result = $channel->pop($this->waitResponseTimeout);
                        if (false === $result)
                        {
                            break;
                        }
                        if ($result['result'] ?? false)
                        {
                            return true;
                        }
                    }
                }
            }
            finally
            {
                ChannelContainer::removeChannel($id);
            }
        }
        else
        {
            if (null === $flag)
            {
                $clientIds = [ConnectionContext::getClientId()];
            }
            else
            {
                $clientIds = ConnectionContext::getClientIdByFlag($flag, $serverName);
                if (!$clientIds)
                {
                    return false;
                }
            }
            foreach ($clientIds as $clientId)
            {
                if ($swooleServer->exists((int) $clientId))
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 获取服务器.
     */
    public function getServer(?string $serverName = null): ?ISwooleServer
    {
        if (null === $serverName)
        {
            /** @var ISwooleServer|null $server */
            $server = RequestContext::getServer();
            if ($server)
            {
                return $server;
            }
            $serverName = 'main';
        }

        // @phpstan-ignore-next-line
        return ServerManager::getServer($serverName, ISwooleServer::class);
    }
}
