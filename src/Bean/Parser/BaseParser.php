<?php

namespace Imi\Bean\Parser;

use Imi\Util\Traits\TSingleton;

abstract class BaseParser implements IParser
{
    use TSingleton;

    /**
     * 注解目标-类.
     */
    const TARGET_CLASS = 'class';

    /**
     * 注解目标-属性.
     */
    const TARGET_PROPERTY = 'property';

    /**
     * 注解目标-方法.
     */
    const TARGET_METHOD = 'method';

    /**
     * 注解目标-常量.
     */
    const TARGET_CONST = 'const';

    /**
     * 数据.
     *
     * @var array
     */
    protected $data = [];

    private function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * 获取数据.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 设置数据.
     *
     * @param array $data
     *
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 是否子类作为单独实例.
     *
     * @return bool
     */
    protected static function isChildClassSingleton()
    {
        return true;
    }
}
