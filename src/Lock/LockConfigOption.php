<?php

namespace Imi\Lock;

use Imi\Util\Traits\TDataToProperty;

/**
 * 锁配置项.
 */
class LockConfigOption
{
    use TDataToProperty;

    /**
     * 类名或 bean 名称.
     *
     * @var string
     */
    public $class;

    /**
     * 配置项.
     *
     * @var array
     */
    public $options = [];
}
