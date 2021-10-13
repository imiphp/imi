<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterFindEventParam;

/**
 * 模型 查找后 事件监听接口.
 */
interface IAfterFindEventListener
{
    public function handle(AfterFindEventParam $e): void;
}
