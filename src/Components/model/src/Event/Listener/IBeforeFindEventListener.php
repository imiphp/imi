<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeFindEventParam;

/**
 * 模型 查找前 事件监听接口.
 */
interface IBeforeFindEventListener
{
    public function handle(BeforeFindEventParam $e): void;
}
