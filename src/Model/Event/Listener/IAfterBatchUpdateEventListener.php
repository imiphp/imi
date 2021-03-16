<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterBatchUpdateEventParam;

/**
 * 模型 批量更新后 事件监听接口.
 */
interface IAfterBatchUpdateEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(AfterBatchUpdateEventParam $e): void;
}
