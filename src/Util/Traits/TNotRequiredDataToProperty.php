<?php
namespace Imi\Util\Traits;

/**
 * 支持提供一个构造方法，传入 $data 将数据直接赋值到当前对象属性中，允许不传构造方法参数
 */
trait TNotRequiredDataToProperty
{
    public function __construct($data = [])
    {
        foreach($data as $k => $v)
        {
            $this->$k = $v;
        }
    }

}