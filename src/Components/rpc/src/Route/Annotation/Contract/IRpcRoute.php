<?php

namespace Imi\Rpc\Route\Annotation\Contract;

interface IRpcRoute
{
    /**
     * 获取 RPC 类型.
     *
     * @return string
     */
    public function getRpcType();
}
