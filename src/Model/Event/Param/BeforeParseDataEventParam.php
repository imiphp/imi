<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeParseDataEventParam extends EventParam
{
    /**
     * 处理前的数据.
     *
     * @var object|array
     */
    public $data;

    /**
     * 对象或模型类名.
     *
     * @var object|string
     */
    public $object;
}
