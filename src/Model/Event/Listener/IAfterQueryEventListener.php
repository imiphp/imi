<?php

declare(strict_types=1);

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterQueryEventParam;

/**
 * 模型 query查询后 事件监听接口
 * 无论是Find、Select，还是通过Model::query()查询，都会触发该事件.
 */
interface IAfterQueryEventListener
{
    public function handle(AfterQueryEventParam $e): void;
}
