<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Server\DataParser\DataParser;
use Imi\Server\Server;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleServerUtil;
use Imi\Swoole\Server\Event\Param\PipeMessageEventParam;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Swoole\Util\Coroutine;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Worker;

/**
 * @Bean(name="LocalServerUtil", env="swoole", recursion=false)
 */
class LocalServerUtil implements ISwooleServerUtil
{
    protected bool $needResponse = true;

    /**
     * 等待响应超时时间.
     */
    protected float $waitResponseTimeout = 30;

    /**
     * {@inheritDoc}
     */
    public function sendMessage(string $action, array $data = [], $workerId = null): int
    {
        $data['action'] = $action;
        $message = json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

        return $this->sendMessageRaw($message, $workerId);
    }

    /**
     * {@inheritDoc}
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
                Coroutine::create(static function () use ($server, $currentWorkerId, $message): void {
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
            $pushParams = (array) $server->getNonControlFrameType();
        }
        else
        {
            $method = 'send';
            $pushParams = [];
        }
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && 'push' === $method)
        {
            $id = static::class . ':' . bin2hex(random_bytes(8));
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
                if ($swooleServer->{$method}($tmpClientId, $data, ...$pushParams))
                {
                    ++$success;
                }
            }
        }

        return $success;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function sendToAll($data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawToAll($dataParser->encode($data, $serverName), $server->getName(), $toAllWorkers);
    }

    /**
     * {@inheritDoc}
     */
    public function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        $success = 0;
        if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $method = 'push';
            $pushParams = (array) $server->getNonControlFrameType();
        }
        else
        {
            $method = 'send';
            $pushParams = [];
        }
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && 'push' === $method)
        {
            $id = static::class . ':' . bin2hex(random_bytes(8));
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
                if ($swooleServer->{$method}($clientId, $data, ...$pushParams))
                {
                    ++$success;
                }
            }
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    public function sendToGroup($groupName, $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawToGroup($groupName, $dataParser->encode($data, $serverName), $server->getName(), $toAllWorkers);
    }

    /**
     * {@inheritDoc}
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
            $pushParams = (array) $server->getNonControlFrameType();
        }
        else
        {
            $method = 'send';
            $pushParams = [];
        }
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && 'push' === $method)
        {
            $id = static::class . ':' . bin2hex(random_bytes(8));
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
                    $result = $group->{$method}($data, ...$pushParams);
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        $result = 0;
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && null !== $flag)
        {
            $id = static::class . ':' . bin2hex(random_bytes(8));
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('closeByFlagRequest', [
                    'messageId'    => $id,
                    'flag'         => $flag,
                    'serverName'   => $server->getName(),
                    'needResponse' => true,
                ]);
                if (isset($channel) && ProcessType::PROCESS !== App::get(ProcessAppContexts::PROCESS_TYPE))
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
     * {@inheritDoc}
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
            $id = static::class . ':' . bin2hex(random_bytes(8));
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('existsRequest', [
                    'messageId'    => $id,
                    'clientId'     => $clientId,
                    'serverName'   => $server->getName(),
                    'needResponse' => true,
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
     * {@inheritDoc}
     */
    public function flagExists(?string $flag, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        $server = $this->getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers && null !== $flag)
        {
            $id = static::class . ':' . bin2hex(random_bytes(8));
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('existsRequest', [
                    'messageId'    => $id,
                    'flag'         => $flag,
                    'serverName'   => $server->getName(),
                    'needResponse' => true,
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
     * {@inheritDoc}
     */
    public function getConnections(?string $serverName = null): array
    {
        return iterator_to_array($this->getServer($serverName)->getSwoolePort()->connections);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectionCount(?string $serverName = null): int
    {
        return $this->getServer($serverName)->getSwoolePort()->connections->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getServer(?string $serverName = null): ?ISwooleServer
    {
        // @phpstan-ignore-next-line
        return Server::getServer($serverName, ISwooleServer::class);
    }
}
