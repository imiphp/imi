<?php

declare(strict_types=1);

namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Contract\IServer;
use Imi\Server\Group\Handler\IGroupHandler;
use Imi\Util\ArrayUtil;

/**
 * 逻辑组.
 *
 * @Bean("ServerGroup")
 *
 * @method array send(string $data, int $extraData = 0)
 * @method array sendfile(string $filename, int $offset =0, int $length = 0)
 * @method array sendwait(string $send_dat)
 * @method array push(string $data, int $opcode = 1, bool $finish = true)
 * @method array close(bool $reset = false)
 */
class Group
{
    /**
     * 服务器对象
     *
     * @var IServer
     */
    protected IServer $server;

    /**
     * 组中最大允许的客户端数量.
     *
     * @var int
     */
    protected int $maxClients = 0;

    /**
     * 组名.
     *
     * @var string
     */
    protected string $groupName = '';

    /**
     * 分组处理器.
     *
     * @var string
     */
    protected string $groupHandler = \Imi\Server\Group\Handler\Redis::class;

    /**
     * 处理器.
     *
     * @var \Imi\Server\Group\Handler\IGroupHandler
     */
    protected IGroupHandler $handler;

    /**
     * 是否启用逻辑分组.
     *
     * @var bool
     */
    protected bool $status = true;

    public function __construct(IServer $server, string $groupName, int $maxClients = -1)
    {
        $this->server = $server;
        $this->groupName = $groupName;
        $this->maxClients = $maxClients;
    }

    public function __init(): void
    {
        if ($this->status)
        {
            $this->handler = $handler = RequestContext::getServerBean($this->groupHandler);
            $handler->createGroup($this->groupName, $this->maxClients);
        }
    }

    /**
     * 获得组处理器对象
     *
     * @return \Imi\Server\Group\Handler\IGroupHandler
     */
    public function getHandler(): IGroupHandler
    {
        return $this->handler;
    }

    /**
     * 加入组.
     *
     * @param int $fd
     *
     * @return void
     */
    public function join(int $fd)
    {
        $groupName = $this->groupName;
        if ($this->handler->joinGroup($groupName, $fd))
        {
            RequestContext::getServerBean('FdMap')->joinGroup($fd, $this);
            ConnectContext::use(function (array $contextData) use ($groupName): array {
                $contextData['__groups'][] = $groupName;

                return $contextData;
            }, $fd);
            Event::trigger('IMI.SERVER.GROUP.JOIN', [
                'server'    => RequestContext::getServer(),
                'groupName' => $groupName,
                'fd'        => $fd,
            ]);
        }
    }

    /**
     * 离开组.
     *
     * @param int $fd
     *
     * @return void
     */
    public function leave(int $fd)
    {
        $groupName = $this->groupName;
        if ($this->handler->leaveGroup($groupName, $fd))
        {
            RequestContext::getServerBean('FdMap')->leaveGroup($fd, $this);
            ConnectContext::use(function (array $contextData) use ($groupName) {
                if (isset($contextData['__groups']))
                {
                    $contextData['__groups'] = ArrayUtil::remove($contextData['__groups'], $groupName);

                    return $contextData;
                }
            }, $fd);
            Event::trigger('IMI.SERVER.GROUP.LEAVE', [
                'server'    => RequestContext::getServer(),
                'groupName' => $groupName,
                'fd'        => $fd,
            ]);
        }
    }

    /**
     * 连接是否存在于组里.
     *
     * @param int $fd
     *
     * @return bool
     */
    public function isInGroup(int $fd): bool
    {
        return $this->handler->isInGroup($this->groupName, $fd);
    }

    /**
     * 获取所有fd.
     *
     * @return int[]
     */
    public function getFds(): array
    {
        return $this->handler->getFds($this->groupName);
    }

    /**
     * 获取组中的连接总数.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->handler->count($this->groupName);
    }

    /**
     * 清空分组.
     *
     * @return void
     */
    public function clear()
    {
        $this->handler->clear();
    }

    /**
     * 获取服务器对象
     *
     * @return IServer
     */
    public function getServer(): IServer
    {
        return $this->server;
    }

    /**
     * 获取组中最大允许的客户端数量.
     *
     * @return int
     */
    public function getMaxClients(): int
    {
        return $this->maxClients;
    }

    /**
     * 魔术方法，返回数组，fd=>执行结果.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return array
     */
    public function __call(string $name, array $arguments)
    {
        $server = $this->server;
        // 要检查的方法名
        static $checkMethods = [
            'close',
            'send',
            'sendfile',
            'sendwait',
            'push',
        ];
        // 客户端关闭的错误
        static $clientCloseErrors = [
            1001,
            1002,
            1003,
            1004,
        ];
        $methodIsCheck = \in_array($name, $checkMethods);
        $result = [];
        /** @var FdMap $fdMap */
        $fdMap = $server->getBean('FdMap');
        foreach ($this->handler->getFds($this->groupName) as $fd)
        {
            // 执行结果
            $result[$fd] = $itemResult = $server->callServerMethod($name, $fd, ...$arguments);
            if ($methodIsCheck && false === $itemResult && \in_array($server->callServerMethod('getLastError'), $clientCloseErrors))
            {
                // 客户端关闭的错误，直接把该客户端T出全部组
                $fdMap->leaveAll($fd);
            }
        }

        return $result;
    }
}
