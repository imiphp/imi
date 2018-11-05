<?php
namespace Imi\Util;

trait TBeanClone
{
    public function __clone()
    {
        if($this->beanProxy)
        {
            $this->beanProxy->setObject($this);
        }
    }
}