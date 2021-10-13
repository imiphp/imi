<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\ErrorLog;
use Imi\Redis\RedisManager;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Worker;

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
        $message = json_encode($data);

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

    public function startSubscribe(): void
    {
        go(function () {
            $redis = RedisManager::getInstance($this->redisName);
            while ($this->subscribeEnable)
            {
                try
                {
                    $redis->subscribe([$this->channel], function (\Redis $redis, string $channel, string $msg) {
                        go(function () use ($msg) {
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
