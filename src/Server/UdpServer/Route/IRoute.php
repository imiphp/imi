<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Route;

interface IRoute
{
    /**
     * 路由解析处理.
     *
     * @param mixed $data
     */
    public function parse($data): ?RouteResult;
}
