<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class HandShakeEventParam extends EventParam
{
    /**
     * swoole 请求对象
     *
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;

    /**
     * swoole 响应对象
     *
     * @var \Imi\Server\Http\Message\Response
     */
    public $response;
}
