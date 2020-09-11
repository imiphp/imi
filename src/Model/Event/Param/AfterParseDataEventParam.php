<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterParseDataEventParam extends EventParam
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

    /**
     * 处理结果.
     *
     * @var \Imi\Util\LazyArrayObject
     */
    public $result;
}
