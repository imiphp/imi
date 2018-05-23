<?php
namespace Imi\Server\Session;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Session\SessionCookie;
use Imi\Bean\Annotation\RequestInject;

/**
 * @Bean("SessionConfig")
 */
class SessionConfig
{
	/**
	 * session名称
	 * @var string
	 */
	public $name;

	/**
	 * @RequestInject("SessionCookie")
	 * @var \Imi\Server\Session\SessionCookie
	 */
	public $cookie;

	/**
	 * 每次请求完成后触发垃圾回收的概率，默认为1%
	 * 可取值0~1.0，概率为0%~100%
	 * @var float
	 */
	public $gcProbability;

	/**
	 * 最大存活时间，默认30天，单位秒
	 * @var int
	 */
	public $maxLifeTime;

	public function __construct(string $name = 'imisid', SessionCookie $cookie = null, float $gcProbability = 0.01, int $maxLifeTime = 86400 * 30)
	{
		$this->name = $name;
		$this->cookie = $cookie;
		$this->gcProbability = $gcProbability;
		$this->maxLifeTime = $maxLifeTime;
	}
}