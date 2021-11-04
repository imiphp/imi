<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\ErrorLog;
use Imi\Redis\RedisManager;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\ServerManager;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Worker;
use Swoole\Coroutine;

/**
 * @Bean(name="RedisServerUtil", env="swoole")
 */
class RedisServerUtil extends LocalServerUtil
{
    /**
     * Redis 连接名称.
     */
    protected ?string $redisName = null;

    /**
     * 发布订阅的频道名.
     */
    protected string $channel = 'imi:RedisServerUtil:channel';

    protected bool $subscribeEnable = true;

    /**
     * @Inject
     */
    protected ErrorLog $errorLog;

    public function __init(): void
    {
        Event::one('IMI.MAIN_SERVER.WORKER.EXIT', function () {
            $this->subscribeEnable = false;
        });
        $this->startSubscribe();
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessage(string $action, array $data = [], $workerId = null): int
    {
        $data['action'] = $action;
        $data['workerId'] = Worker::getWorkerId();
        $message = json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

        return $this->sendMessageRaw($message, $workerId);
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessageRaw(string $message, $workerId = null): int
    {
        // 只发给所有进程
        $redis = RedisManager::getInstance($this->redisName);
        $result = $redis->publish($this->channel, $message);

        return $result ?: 0;
    }

    /**
     * {@inheritDoc}
     */
    public function sendByFlag($data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }

        return $this->sendRawByFlag($dataParser->encode($data, $serverName), $flag, $serverName, $toAllWorkers);
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

            return $this->sendRaw($data, $clientIds, $serverName, $toAllWorkers);
        }
        elseif ($toAllWorkers)
        {
            $server = $this->getServer($serverName);
            $success = 0;
            foreach ((array) $flag as $tmpFlag)
            {
                $id = uniqid('', true);
                try
                {
                    if ($this->needResponse)
                    {
                        $channel = ChannelContainer::getChannel($id);
                    }
                    $count = $this->sendMessage('sendRawByFlagRequest', [
                        'messageId'    => $id,
                        'flag'         => $tmpFlag,
                        'data'         => $data,
                        'serverName'   => $server->getName(),
                        'needResponse' => $this->needResponse,
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

            return $success;
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

            return $this->sendRaw($data, $clientIds, $serverName, $toAllWorkers);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        if (null === $flag)
        {
            return $this->close(null, $serverName, $toAllWorkers);
        }
        elseif ($toAllWorkers)
        {
            if (null === $serverName)
            {
                $server = $this->getServer($serverName);
                if (!$server)
                {
                    return 0;
                }
                $serverName = $server->getName();
            }
            $success = 0;
            $id = uniqid('', true);
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('closeByFlagRequest', [
                    'messageId'    => $id,
                    'flag'         => $flag,
                    'serverName'   => $serverName,
                    'needResponse' => $this->needResponse,
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

            return $success;
        }
        else
        {
            return parent::closeByFlag($flag, $serverName, false);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function flagExists(?string $flag, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        if (null === $flag)
        {
            return $this->flagExists(null, $serverName, $toAllWorkers);
        }
        elseif ($toAllWorkers)
        {
            if (null === $serverName)
            {
                $server = $this->getServer($serverName);
                if (!$server)
                {
                    return false;
                }
                $serverName = $server->getName();
            }
            $id = uniqid('', true);
            try
            {
                if ($this->needResponse)
                {
                    $channel = ChannelContainer::getChannel($id);
                }
                $count = $this->sendMessage('existsRequest', [
                    'messageId'    => $id,
                    'flag'         => $flag,
                    'serverName'   => $serverName,
                    'needResponse' => $this->needResponse,
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
                if ($this->needResponse)
                {
                    ChannelContainer::removeChannel($id);
                }
            }

            return false;
        }
        else
        {
            return parent::flagExists($flag, $serverName, false);
        }
    }

    public function startSubscribe(): void
    {
        Coroutine::create(function () {
            $redis = RedisManager::getInstance($this->redisName);
            while ($this->subscribeEnable)
            {
                try
                {
                    $redis->subscribe([$this->channel], function (\Redis $redis, string $channel, string $msg) {
                        Coroutine::create(function () use ($msg) {
                            $data = json_decode($msg, true);
                            if (!isset($data['action'], $data['serverName']))
                            {
                                return;
                            }
                            RequestContext::set('server', ServerManager::getServer($data['serverName']));
                            Event::trigger('IMI.PIPE_MESSAGE.' . $data['action'], [
                                'data' => $data,
                            ], $this);
                        });
                    });
                }
                catch (\Throwable $e)
                {
                    $this->errorLog->onException($e);
                    sleep(3); // 等待 3 秒重试
                }
            }
        });
    }
}
