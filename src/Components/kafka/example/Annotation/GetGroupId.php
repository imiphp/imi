<?php

declare(strict_types=1);

namespace KafkaApp\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\Annotation\Inherit;
use Imi\Util\Imi;

/**
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @property callable $callable 回调
 */
#[\Attribute]
class GetGroupId extends BaseInjectValue
{
    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        if (Imi::checkAppType('swoole'))
        {
            return 'test-consumer-swoole';
        }
        else
        {
            return 'test-consumer-workerman';
        }
    }
}
