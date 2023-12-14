<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Model\Model;

class BeforeParseDataEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 处理前的数据.
         */
        public object|array $data,

        /**
         * 模型对象.
         */
        public ?Model $object
    ) {
        parent::__construct($__eventName);
    }
}
