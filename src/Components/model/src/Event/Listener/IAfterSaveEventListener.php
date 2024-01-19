<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterSaveEventParam;

/**
 * 模型 保存后 事件监听接口.
 */
interface IAfterSaveEventListener
{
    public function handle(AfterSaveEventParam $e): void;
}
