<?php
namespace Imi\Pool;

use Imi\Bean\Annotation\Bean;
use Imi\Pool\Interfaces\IPoolConfig;

/**
 * 池子配置
 * @Bean("PoolConfig")
 */
class PoolConfig implements IPoolConfig
{
	/**
	 * 池子中最多资源数
	 * @var integer
	 */
	protected $maxResources = 10;

	/**
	 * 池子中最少资源数
	 * @var integer
	 */
	protected $minResources = 2;

	/**
	 * 获取资源回收时间间隔，单位：秒
	 * @var integer
	 */
	protected $gcInterval = 60;

	/**
	 * 获取资源最大存活时间，单位：秒
	 * @var integer
	 */
	protected $maxActiveTime = 3600;

	/**
	 * 等待资源最大超时时间，单位：毫秒
	 * @var integer
	 */
	protected $waitTimeout = 3000;

	public function __construct($option = [])
	{
		foreach($option as $k => $v)
		{
			$this->$k = $v;
		}
	}

	/**
	 * 池子中最多资源数
	 * @return int
	 */
	public function getMaxResources()
	{
		return $this->maxResources;
	}

	/**
	 * 池子中最少资源数
	 * @return int
	 */
	public function getMinResources()
	{
		return $this->minResources;
	}

	/**
	 * 获取资源回收时间间隔，单位：秒
	 * @return int
	 */
	public function getGCInterval()
	{
		return $this->gcInterval;
	}

	/**
	 * 获取资源最大存活时间，单位：秒
	 * @return int
	 */
	public function getMaxActiveTime()
	{
		return $this->maxActiveTime;
	}

	/**
	 * 获取等待资源最大超时时间，单位：毫秒
	 * @return int
	 */
	public function getWaitTimeout()
	{
		return $this->waitTimeout;
	}

	/**
	 * Set 池子名称
	 *
	 * @param string $name 池子名称
	 *
	 * @return static
	 */ 
	public function setName(string $name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Set 池子中最多资源数
	 *
	 * @param integer $maxResources 池子中最多资源数
	 *
	 * @return static
	 */ 
	public function setMaxResources($maxResources)
	{
		$this->maxResources = $maxResources;

		return $this;
	}

	/**
	 * Set 池子中最少资源数
	 *
	 * @param integer $minResources 池子中最少资源数
	 *
	 * @return static
	 */ 
	public function setMinResources($minResources)
	{
		$this->minResources = $minResources;

		return $this;
	}

	/**
	 * Set 资源回收时间间隔，单位：秒
	 *
	 * @param integer $gcInterval 获取资源回收时间间隔，单位：秒
	 *
	 * @return static
	 */ 
	public function setGcInterval($gcInterval)
	{
		$this->gcInterval = $gcInterval;

		return $this;
	}

	/**
	 * Set 资源最大存活时间，单位：秒
	 *
	 * @param integer $maxActiveTime 获取资源最大存活时间，单位：秒
	 *
	 * @return static
	 */ 
	public function setMaxActiveTime($maxActiveTime)
	{
		$this->maxActiveTime = $maxActiveTime;

		return $this;
	}

	/**
	 * Set 等待资源最大超时时间
	 * @param integer $waitTimeout 等待资源最大超时时间
	 *
	 * @return static
	 */ 
	public function setWaitTimeout($waitTimeout)
	{
		$this->waitTimeout = $waitTimeout;

		return $this;
	}
}