<?php
namespace Imi\Bean\Annotation;

/**
 * 注解基类
 */
abstract class Base
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName;

    public function __construct($data = [])
    {
        if(null !== $this->defaultFieldName && isset($data['value']) && 1 === count($data))
        {
            // 只传一个参数处理
            $this->{$this->defaultFieldName} = $data['value'];
            return;
        }
        foreach($data as $k => $v)
        {
            $this->$k = $v;
        }
    }
}