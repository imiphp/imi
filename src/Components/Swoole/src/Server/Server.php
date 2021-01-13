<?php

declare(strict_types=1);

namespace Imi\Swoole\Server;

use Imi\Event\Event;
use Imi\RequestContext;
use Imi\ServerManage;
use Imi\Swoole\Server\DataParser\DataParser;
use Imi\Swoole\Server\Event\Param\PipeMessageEventParam;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Swoole\Worker;

/**
 * 服务器工具类.
 */
class Server
{
    private function __construct()
    {
    }

    /**
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param string         $action
     * @param array          $data
     * @param int|int[]|null $workerId
     *
     * @return int
     */
    public static function sendMessage(string $action, array $data = [], $workerId = null): int
    {
        if (null === $workerId)
        {
            $workerId = range(0, Worker::getWorkerNum() - 1);
        }
        $data['action'] = $action;
        $message = json_encode($data);
        $server = ServerManage::getServer('main');
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
     * 发送消息给 Worker 进程.
     *
     * 返回成功发送消息数量
     *
     * @param string         $message
     * @param int|int[]|null $workerId
     *
     * @return int
     */
    public static function sendMessageRaw(string $message, $workerId = null): int
    {
        if (null === $workerId)
        {
            $workerId = range(0, Worker::getWorkerNum() - 1);
        }
        $server = ServerManage::getServer('main');
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
     * @param mixed          $data
     * @param int|int[]|null $fd         为 null 时，则发送给当前连接
     * @param string|null    $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public static function send($data, $fd = null, $serverName = null): int
    {
        $server = static::getServer($serverName);
        /** @var \Imi\Swoole\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return static::sendRaw($dataParser->encode($data, $serverName), $fd, $server->getName());
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * @param string         $data
     * @param int|int[]|null $fd         为 null 时，则发送给当前连接
     * @param string|null    $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public static function sendRaw(string $data, $fd = null, ?string $serverName = null): int
    {
        $server = static::getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $method = 'push';
        }
        else
        {
            $method = 'send';
        }
        if (null === $fd)
        {
            $fd = RequestContext::get('fd');
            if (!$fd)
            {
                return 0;
            }
        }
        $success = 0;
        foreach ((array) $fd as $tmpFd)
        {
            if ($swooleServer->$method($tmpFd, $data))
            {
                ++$success;
            }
        }

        return $success;
    }

    /**
     * 发送数据给所有客户端.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed       $data
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     *
     * @return int
     */
    public static function sendToAll($data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = static::getServer($serverName);
        /** @var \Imi\Swoole\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return static::sendRawToAll($dataParser->encode($data, $serverName), $server->getName(), $toAllWorkers);
    }

    /**
     * 发送数据给所有客户端.
     *
     * 数据原样发送
     *
     * @param string      $data
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     *
     * @return int
     */
    public static function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = static::getServer($serverName);
        $swooleServer = $server->getSwooleServer();
        $success = 0;
        if (\SWOOLE_BASE === $swooleServer->mode && $toAllWorkers)
        {
            $id = uniqid('', true);
            try
            {
                $channel = ChannelContainer::getChannel($id);
                static::sendMessage('sendRawToAllRequest', [
                    'messageId'     => $id,
                    'data'          => $data,
                    'serverName'    => $server->getName(),
                ]);
                for ($i = Worker::getWorkerNum(); $i > 0; --$i)
                {
                    $result = $channel->pop(30);
                    if (false === $result)
                    {
                        break;
                    }
                    $success += ($result['result'] ?? 0);
                }
            }
            finally
            {
                ChannelContainer::removeChannel($id);
            }
        }
        else
        {
            if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
            {
                $method = 'push';
            }
            else
            {
                $method = 'send';
            }
            foreach ($server->getSwoolePort()->connections as $fd)
            {
                if ($swooleServer->$method($fd, $data))
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
     * @param string|null     $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public static function sendToGroup($groupName, $data, ?string $serverName = null): int
    {
        $server = static::getServer($serverName);
        /** @var \Imi\Swoole\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return static::sendRawToGroup($groupName, $dataParser->encode($data, $serverName), $server->getName());
    }

    /**
     * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
     *
     * 数据原样发送
     *
     * @param string|string[] $groupName
     * @param string          $data
     * @param string|null     $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public static function sendRawToGroup($groupName, string $data, ?string $serverName = null): int
    {
        $server = static::getServer($serverName);
        if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $method = 'push';
        }
        else
        {
            $method = 'send';
        }
        $success = 0;
        foreach ((array) $groupName as $tmpGroupName)
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

        return $success;
    }

    /**
     * 获取服务器.
     *
     * @param string|null $serverName
     *
     * @return \Imi\Swoole\Server\Base|null
     */
    public static function getServer(?string $serverName = null): ?Base
    {
        if (null === $serverName)
        {
            $server = RequestContext::getServer();
            if ($server)
            {
                return $server;
            }
            $serverName = 'main';
        }

        return ServerManage::getServer($serverName);
    }
}
