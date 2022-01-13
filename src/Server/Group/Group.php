<?php

declare(strict_types=1);

namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
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
     */
    protected IServer $server;

    /**
     * 组中最大允许的客户端数量.
     */
    protected int $maxClients = 0;

    /**
     * 组名.
     */
    protected string $groupName = '';

    /**
     * 分组处理器.
     */
    protected string $groupHandler = 'GroupLocal';

    /**
     * 处理器.
     */
    protected IGroupHandler $handler;

    /**
     * 是否启用逻辑分组.
     */
    protected bool $status = true;

    public function __construct(IServer $server, string $groupName, int $maxClients = -1)
    {
        $this->server = $server;
        $this->groupName = $groupName;
        $this->maxClients = $maxClients;
    }

    /**
     * 获得组处理器对象
     */
    public function getHandler(): ?IGroupHandler
    {
        if ($this->status)
        {
            if (isset($this->handler))
            {
                return $this->handler;
            }
            else
            {
                $this->handler = $handler = $this->server->getBean($this->groupHandler);
                $handler->createGroup($this->groupName, $this->maxClients);

                return $handler;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * 启动时执行.
     */
    public function startup(): void
    {
        $handler = $this->getHandler();
        if (!$handler)
        {
            return;
        }
        $handler->startup();
    }

    /**
     * 加入组.
     *
     * @param int|string $clientId
     */
    public function join($clientId): void
    {
        $groupName = $this->groupName;
        if ($this->getHandler()->joinGroup($groupName, $clientId))
        {
            // @phpstan-ignore-next-line
            $this->server->getBean('ClientIdMap')->joinGroup($clientId, $this);
            ConnectionContext::use(static function (array $contextData) use ($groupName): array {
                $contextData['__groups'][] = $groupName;

                return $contextData;
            }, $clientId, $this->server->getName());
            Event::trigger('IMI.SERVER.GROUP.JOIN', [
                'server'          => $this->server,
                'groupName'       => $groupName,
                'clientId'        => $clientId,
            ]);
        }
    }

    /**
     * 离开组.
     *
     * @param int|string $clientId
     */
    public function leave($clientId): void
    {
        $groupName = $this->groupName;
        if ($this->getHandler()->leaveGroup($groupName, $clientId))
        {
            // @phpstan-ignore-next-line
            $this->server->getBean('ClientIdMap')->leaveGroup($clientId, $this);
            ConnectionContext::use(static function (array $contextData) use ($groupName) {
                if (isset($contextData['__groups']))
                {
                    $contextData['__groups'] = ArrayUtil::remove($contextData['__groups'], $groupName);

                    return $contextData;
                }
            }, $clientId, $this->server->getName());
            Event::trigger('IMI.SERVER.GROUP.LEAVE', [
                'server'          => $this->server,
                'groupName'       => $groupName,
                'clientId'        => $clientId,
            ]);
        }
    }

    /**
     * 连接是否存在于组里.
     *
     * @param int|string $clientId
     */
    public function isInGroup($clientId): bool
    {
        return $this->getHandler()->isInGroup($this->groupName, $clientId);
    }

    /**
     * 获取所有连接ID.
     *
     * @return int[]|string[]
     */
    public function getClientIds(): array
    {
        return $this->getHandler()->getClientIds($this->groupName);
    }

    /**
     * 获取组中的连接总数.
     */
    public function count(): int
    {
        return $this->getHandler()->count($this->groupName);
    }

    /**
     * 清空分组.
     */
    public function clear(): void
    {
        $this->getHandler()->clear();
    }

    /**
     * 获取服务器对象
     */
    public function getServer(): IServer
    {
        return $this->server;
    }

    /**
     * 获取组中最大允许的客户端数量.
     */
    public function getMaxClients(): int
    {
        return $this->maxClients;
    }

    /**
     * 魔术方法，返回数组，clientId=>执行结果.
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
        /** @var ClientIdMap $clientIdMap */
        $clientIdMap = $server->getBean('ClientIdMap');
        $clientIds = $this->handler->getClientIds($this->groupName);
        if ($clientIds)
        {
            foreach ($clientIds as $clientId)
            {
                // 执行结果
                $result[$clientId] = $itemResult = $server->callServerMethod($name, $clientId, ...$arguments);
                if ($methodIsCheck && false === $itemResult && \in_array($server->callServerMethod('getLastError'), $clientCloseErrors))
                {
                    // 客户端关闭的错误，直接把该客户端T出全部组
                    $clientIdMap->leaveAll($clientId);
                }
            }
        }

        return $result;
    }
}
