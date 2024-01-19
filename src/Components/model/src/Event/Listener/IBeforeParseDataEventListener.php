<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeParseDataEventParam;

/**
 * 模型 处理 save、insert、update 数据前 事件监听接口.
 */
interface IBeforeParseDataEventListener
{
    public function handle(BeforeParseDataEventParam $e): void;
}
