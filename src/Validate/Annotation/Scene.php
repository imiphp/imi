<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 场景定义.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class Scene extends Base
{
    /**
     * 场景名称.
     *
     * @var string
     */
    public $name;

    /**
     * 需要验证的字段名列表.
     *
     * @var array
     */
    public $fields = [];
}
