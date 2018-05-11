<?php
namespace Imi\Server\Route;

interface IRoute
{
	public function parse(BaseRouteParam $param);
}