<?php
namespace Imi\Pool\Interfaces;

/**
 * 池子配置接口
 */
interface IPoolConfig
{
	/**
	 * 池子名称
	 * @return string
	 */
	public function getName();

	/**
	 * 池子中最多资源数
	 * @return int
	 */
	public function getMaxResources();

	/**
	 * 池子中最少资源数
	 * @return int
	 */
	public function getMinResources();

	/**
	 * 获取资源回收时间间隔，单位：秒
	 * @return int
	 */
	public function getGCInterval();

	/**
	 * 获取资源最大存活时间，单位：秒
	 * @return int
	 */
	public function getMaxActiveTime();

	/**
	 * 获取等待资源最大超时时间，单位：毫秒
	 * @return int
	 */
	public function getWaitTimeout();
}