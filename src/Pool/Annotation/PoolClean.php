<?php

declare(strict_types=1);

namespace Imi\Pool\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 在方法上使用本注解，可以在调用该方法时，剔除或保留指定连接池（仅推荐在Tool、Process中使用）.
 *
 * @Annotation
 * @Target({"METHOD"})
 *
 * @property string $mode 模式；allow-白名单；deny-黑名单
 * @property array  $list 连接池名称列表
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class PoolClean extends Base
{
    public function __construct(?array $__data = null, string $mode = 'allow', array $list = [])
    {
        parent::__construct(...\func_get_args());
    }
}
