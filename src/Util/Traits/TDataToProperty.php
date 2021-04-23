<?php

namespace Imi\Util\Traits;

/**
 * 支持提供一个构造方法，传入 $data 将数据直接赋值到当前对象属性中.
 */
trait TDataToProperty
{
    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        if ($data)
        {
            foreach ($data as $k => $v)
            {
                $this->$k = $v;
            }
        }
    }
}
