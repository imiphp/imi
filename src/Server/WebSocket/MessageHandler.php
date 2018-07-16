<?php
namespace Imi\Server\WebSocket;

use Imi\App;
use Imi\RequestContext;
use Imi\Server\WebSocket\Message\IFrame;

class MessageHandler implements IMessageHandler
{
	/**
	 * 中间件数组
	 * @var string[]
	 */
	protected $middlewares = [];

	/**
	 * 当前执行第几个
	 * @var int
	 */
	protected $index = 0;

	/**
	 * 构造方法
	 * @param string[] $middlewares 中间件数组
	 */
	public function __construct(array $middlewares)
	{
		$this->middlewares = $middlewares;
	}

	/**
	 * 返回值为响应内容，为null则无任何响应
	 * @param IFrame $request
	 * @return mixed
	 */
    public function handle(IFrame $request)
	{
		if(isset($this->middlewares[$this->index]))
		{
			$middleware = $this->middlewares[$this->index];
			if($middleware instanceof RequestHandlerInterface)
			{
				$requestHandler = $middleware;
			}
			else
			{
				$requestHandler = RequestContext::getServerBean($middleware);
			}
		}
		else
		{
			return null;
		}
		return $requestHandler->process($request, $this->next());
	}

	/**
	 * 获取下一个RequestHandler对象
	 * @return static
	 */
	protected function next()
	{
		$self = clone $this;
		++$self->index;
		return $self;
	}

	/**
	 * 是否是最后一个
	 * @return boolean
	 */
	public function isLast()
	{
		return !isset($this->middlewares[$this->index]);
	}
	
}