<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeDeleteEventParam;

/**
 * 模型 删除前 事件监听接口.
 */
interface IBeforeDeleteEventListener
{
    public function handle(BeforeDeleteEventParam $e): void;
}
