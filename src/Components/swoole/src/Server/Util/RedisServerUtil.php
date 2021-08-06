<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\ErrorLog;
use Imi\Redis\RedisManager;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\ServerManager;
use Imi\Worker;

/**
 * @Bean("RedisServerUtil")
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

    // protected bool $needResponse = false;

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
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public function sendMessage(string $action, array $data = [], $workerId = null): int
    {
        $data['action'] = $action;
        $data['workerId'] = Worker::getWorkerId();
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
        // 只发给所有进程
        $redis = RedisManager::getInstance($this->redisName);
        $result = $redis->publish($this->channel, $message);

        return $result ?: 0;
    }

    // /**
    //  * 发送数据给指定客户端，支持一个或多个（数组）.
    //  *
    //  * @param int|int[]|string|string[]|null $clientId     为 null 时，则发送给当前连接
    //  * @param string|null                    $serverName   服务器名，默认为当前服务器或主服务器
    //  * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
    //  */
    // public function sendRaw(string $data, $clientId = null, ?string $serverName = null, bool $toAllWorkers = true): int
    // {
    //     return parent::sendRaw($data, $clientId, $serverName, false);
    // }

    // /**
    //  * 发送数据给指定标记的客户端，支持一个或多个（数组）.
    //  *
    //  * 数据将会通过处理器编码
    //  *
    //  * @param mixed                $data
    //  * @param string|string[]|null $flag         为 null 时，则发送给当前连接
    //  * @param string|null          $serverName   服务器名，默认为当前服务器或主服务器
    //  * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
    //  */
    // public function sendByFlag($data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    // {
    //     $server = $this->getServer($serverName);
    //     if (!$server || !$server->isLongConnection())
    //     {
    //         return 0;
    //     }

    //     /** @var \Imi\Server\DataParser\DataParser $dataParser */
    //     $dataParser = $server->getBean(DataParser::class);

    //     return $this->sendRawByFlag($dataParser->encode($data, $serverName), $flag, $serverName, $toAllWorkers);
    // }

    // /**
    //  * 发送数据给指定标记的客户端，支持一个或多个（数组）.
    //  *
    //  * @param string|string[]|null $flag         为 null 时，则发送给当前连接
    //  * @param string|null          $serverName   服务器名，默认为当前服务器或主服务器
    //  * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
    //  */
    // public function sendRawByFlag(string $data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    // {
    //     if ($toAllWorkers)
    //     {
    //         return $this->sendMessage('sendRawByFlagRequest', [
    //             'data'         => $data,
    //             'flag'         => $flag,
    //             'serverName'   => $serverName ?? (RequestContext::getServer()->getName()),
    //             'needResponse' => $this->needResponse,
    //         ]);
    //     }
    //     else
    //     {
    //         return parent::sendRawByFlag($data, $flag, $serverName, $toAllWorkers);
    //     }
    // }

    // /**
    //  * 发送数据给所有客户端.
    //  *
    //  * 数据原样发送
    //  *
    //  * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
    //  * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
    //  */
    // public function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    // {
    //     if ($toAllWorkers)
    //     {
    //         return $this->sendMessage('sendRawToAllRequest', [
    //             'data'         => $data,
    //             'serverName'   => $serverName ?? (RequestContext::getServer()->getName()),
    //             'needResponse' => $this->needResponse,
    //         ]);
    //     }
    //     else
    //     {
    //         return parent::sendRawToAll($data, $serverName, $toAllWorkers);
    //     }
    // }

    // /**
    //  * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
    //  *
    //  * 数据原样发送
    //  *
    //  * @param string|string[] $groupName
    //  * @param string|null     $serverName   服务器名，默认为当前服务器或主服务器
    //  * @param bool            $toAllWorkers BASE模式下，发送给所有 worker 中的连接
    //  */
    // public function sendRawToGroup($groupName, string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    // {
    //     if ($toAllWorkers)
    //     {
    //         return $this->sendMessage('sendToGroupsRequest', [
    //             'groups'       => $groupName,
    //             'data'         => $data,
    //             'serverName'   => $serverName ?? (RequestContext::getServer()->getName()),
    //             'needResponse' => $this->needResponse,
    //         ]);
    //     }
    //     else
    //     {
    //         return parent::sendRawToGroup($groupName, $data, $serverName, $toAllWorkers);
    //     }
    // }

    // /**
    //  * 关闭一个或多个指定标记的连接.
    //  *
    //  * @param string|string[]|null $flag
    //  * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
    //  */
    // public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    // {
    //     if ($toAllWorkers)
    //     {
    //         return $this->sendMessage('closeByFlagRequest', [
    //             'flag'         => $flag,
    //             'serverName'   => $serverName ?? (RequestContext::getServer()->getName()),
    //             'needResponse' => $this->needResponse,
    //         ]);
    //     }
    //     else
    //     {
    //         return parent::closeByFlag($flag, $serverName, $toAllWorkers);
    //     }
    // }

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
