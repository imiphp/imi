<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route;

interface IRoute
{
    /**
     * 路由解析处理.
     *
     * @param mixed $data
     *
     * @return RouteResult|null
     */
    public function parse($data): ?RouteResult;
}
