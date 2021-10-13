<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\InitEventParam;

/**
 * 模型 初始化 事件监听接口.
 */
interface IInitEventListener
{
    public function handle(InitEventParam $e): void;
}
