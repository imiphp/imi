<?php

declare(strict_types=1);

namespace Imi\Bean\Traits;

use Imi\Bean\BeanFactory;
use Imi\Bean\BeanProxy;

/**
 * 自动注入属性.
 */
trait TAutoInject
{
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
        BeanProxy::injectProps($this, BeanFactory::getObjectClass($this));
    }
}
