<?php
namespace Imi\Util\Traits;

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