<?php

namespace Imi\Bean\Traits;

use Imi\Bean\BeanFactory;

/**
 * 自动注入属性.
 */
trait TAutoInject
{
    /**
     * beanProxy.
     *
     * @var \Imi\Bean\BeanProxy
     */
    protected $beanProxy;

    public function __construct()
    {
        $this->__autoInject();
    }

    /**
     * 自动注入属性.
     *
     * @return void
     */
    protected function __autoInject()
    {
        $this->beanProxy = new \Imi\Bean\BeanProxy($this);
        BeanFactory::initInstance($this);
    }
}
