<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterParseDataEventParam;

/**
 * 模型 处理 save、insert、update 数据后 事件监听接口.
 */
interface IAfterParseDataEventListener
{
    public function handle(AfterParseDataEventParam $e): void;
}
