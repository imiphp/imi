<?php

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 提取字段中的属性到当前模型.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class ExtractProperty extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'fieldName';

    /**
     * 字段名，支持.的形式无限级取值
     *
     * @var string
     */
    public $fieldName;

    /**
     * 提取到当前模型中的字段别名，不设置默认为原始字段名.
     *
     * @var string
     */
    public $alias;
}
