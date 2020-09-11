<?php

namespace Imi\Pool\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 在方法上使用本注解，可以在调用该方法时，剔除或保留指定连接池（仅推荐在Tool、Process中使用）.
 *
 * @Annotation
 * @Target({"METHOD"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class PoolClean extends Base
{
    /**
     * 模式
     * allow-白名单
     * deny-黑名单.
     *
     * @var string
     */
    public $mode = 'allow';

    /**
     * 连接池名称列表.
     *
     * @var array
     */
    public $list = [];
}
