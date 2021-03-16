<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeBatchDeleteEventParam;

/**
 * 模型 批量删除前 事件监听接口.
 */
interface IBeforeBatchDeleteEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(BeforeBatchDeleteEventParam $e): void;
}
