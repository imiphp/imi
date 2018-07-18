<?php
namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Group\Exception\JoinGroupException;
use Imi\Server\Group\Exception\LeaveGroupException;
use Imi\Server\Group\Exception\MethodNotFoundException;
use Imi\Server\Group\Handler\IGroupHandler;
use Imi\RequestContext;

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
	 * @var \Swoole\Server
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

	public function __construct(\Swoole\Server $server, string $groupName, int $maxClients = -1)
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
	 * @throws \Imi\Server\Group\Exception\JoinGroupException
	 */
	public function join($fd)
	{
		$this->handler->joinGroup($this->groupName, $fd);
	}

	/**
	 * 离开组
	 *
	 * @param int $fd
	 * @return void
	 * @throws \Imi\Server\Group\Exception\JoinGroupException
	 */
	public function leave($fd)
	{
		$this->handler->leaveGroup($this->groupName, $fd);
	}

	/**
	 * 获取服务器对象
	 *
	 * @return \Swoole\Server
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
		if(!method_exists($this->server, $name))
		{
			throw new MethodNotFoundException(sprintf('%s->%s() method is not exists', get_class($this->server), $name));
		}
		$result = [];
		foreach($this->handler->getFds($this->groupName) as $fd)
		{
			$result[$fd] = $this->server->$name($fd, ...$arguments);
		}
		return $result;
    }
}