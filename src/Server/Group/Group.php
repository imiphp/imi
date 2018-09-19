<?php
namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Group\Exception\MethodNotFoundException;
use Imi\Server\Group\Handler\IGroupHandler;
use Imi\RequestContext;
use Imi\Event\Event;

/**
 * 逻辑组
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
     * @var \Imi\Server\Base
     */
    protected $server;

    /**
     * 组中最大允许的客户端数量
     *
     * @var int
     */
    protected $maxClients;

    /**
     * 组名
     *
     * @var string
     */
    protected $groupName;

    /**
     * 分组处理器
     *
     * @var string
     */
    protected $groupHandler = \Imi\Server\Group\Handler\Redis::class;

    /**
     * 处理器
     *
     * @var \Imi\Server\Group\Handler\IGroupHandler
     */
    protected $handler;

    public function __construct(\Imi\Server\Base $server, string $groupName, int $maxClients = -1)
    {
        $this->server = $server;
        $this->groupName = $groupName;
        $this->maxClients = $maxClients;
    }

    public function __init()
    {
        $this->handler = RequestContext::getServerBean($this->groupHandler);
        $this->handler->createGroup($this->groupName, $this->maxClients);
    }

    /**
     * 加入组
     * 
     * @param int $fd
     * @return void
     */
    public function join($fd)
    {
        if($this->handler->joinGroup($this->groupName, $fd))
        {
            RequestContext::getServerBean('FdMap')->joinGroup($fd, $this);
            Event::trigger('IMI.SERVER.GROUP.JOIN', [
                'server'        =>    RequestContext::getServer(),
                'groupName'        =>    $this->groupName,
                'fd'            =>    $fd,
            ]);
        }
    }

    /**
     * 离开组
     *
     * @param int $fd
     * @return void
     */
    public function leave($fd)
    {
        if($this->handler->leaveGroup($this->groupName, $fd))
        {
            RequestContext::getServerBean('FdMap')->leaveGroup($fd, $this);
            Event::trigger('IMI.SERVER.GROUP.LEAVE', [
                'server'        =>    RequestContext::getServer(),
                'groupName'        =>    $this->groupName,
                'fd'            =>    $fd,
            ]);
        }
    }

    /**
     * 获取组中的连接总数
     * @return integer
     */
    public function count()
    {
        return $this->handler->count($this->groupName);
    }

    /**
     * 获取服务器对象
     *
     * @return \Imi\Server\Base
     */ 
    public function getServer()
    {
        return $this->server;
    }

    /**
     * 获取组中最大允许的客户端数量
     *
     * @return int
     */ 
    public function getMaxClients()
    {
        return $this->maxClients;
    }

    /**
     * 魔术方法，返回数组，fd=>执行结果
     * @param string $name
     * @param array $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        $server = $this->server->getSwooleServer();
        if(!method_exists($server, $name))
        {
            throw new MethodNotFoundException(sprintf('%s->%s() method is not exists', get_class($server), $name));
        }
        // 要检查的方法名
        static $checkMethods = [
            'close',
            'send',
            'sendfile',
            'sendwait',
            'push'
        ];
        // 客户端关闭的错误
        static $clientCloseErrors = [
            1001,
            1002,
            1003,
            1004,
        ];
        $methodIsCheck = in_array($name, $checkMethods);
        $result = [];
        $fdMap = RequestContext::getServerBean('FdMap');
        foreach($this->handler->getFds($this->groupName) as $fd)
        {
            // 执行结果
            $result[$fd] = $itemResult = $server->$name($fd, ...$arguments);
            if($methodIsCheck && false === $itemResult)
            {
                if(in_array($server->getLastError(), $clientCloseErrors))
                {
                    // 客户端关闭的错误，直接把该客户端T出全部组
                    $fdMap->leaveAll($fd);
                }
            }
        }
        return $result;
    }
}