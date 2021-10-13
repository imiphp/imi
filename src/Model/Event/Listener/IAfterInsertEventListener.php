<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterInsertEventParam;

/**
 * 模型 插入后 事件监听接口.
 */
interface IAfterInsertEventListener
{
    public function handle(AfterInsertEventParam $e): void;
}
