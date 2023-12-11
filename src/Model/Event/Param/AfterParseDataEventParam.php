<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;

class AfterParseDataEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 处理前的数据.
         */
        public readonly object|array $data,

        /**
         * 对象或模型类名.
         */
        public readonly object|string $object,

        /**
         * 处理结果.
         */
        public \Imi\Util\LazyArrayObject $result
    ) {
        parent::__construct($__eventName);
    }
}
