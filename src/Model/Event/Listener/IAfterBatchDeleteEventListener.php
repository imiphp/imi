<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterBatchDeleteEventParam;

/**
 * 模型 批量删除后 事件监听接口.
 */
interface IAfterBatchDeleteEventListener
{
    public function handle(AfterBatchDeleteEventParam $e): void;
}
