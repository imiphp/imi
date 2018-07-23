<?php
namespace Imi\Server\WebSocket\Parser;

use Imi\RequestContext;

/**
 * 数据处理器
 */
class DataParser
{
	/**
	 * 处理类名
	 * @var string
	 */
	protected $parserClass = JsonObjectParser::class;

	/**
	 * 编码为存储格式
	 * @param mixed $data
	 * @return mixed
	 */
	public function encode($data)
	{
		return RequestContext::getServerBean($this->parserClass)->encode($data);
	}

	/**
	 * 解码为php变量
	 * @param mixed $data
	 * @return mixed
	 */
	public function decode($data)
	{
		return RequestContext::getServerBean($this->parserClass)->decode($data);
	}
}