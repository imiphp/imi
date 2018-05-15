<?php
namespace Imi\Server\Route;

interface IRoute
{
	/**
	 * 路由解析处理
	 * @param BaseRouteParam $param
	 * @return array
	 */
	public function parse(BaseRouteParam $param);
}