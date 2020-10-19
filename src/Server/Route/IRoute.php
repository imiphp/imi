<?php

namespace Imi\Server\Route;

use Imi\Server\Http\Message\Request;

interface IRoute
{
    /**
     * 路由解析处理.
     *
     * @param Request $request
     *
     * @return array
     */
    public function parse(Request $request);
}
