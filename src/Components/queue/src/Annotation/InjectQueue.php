<?php

declare(strict_types=1);

namespace Imi\Queue\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Inherit;

/**
 * 注入队列对象
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[Inherit]
class InjectQueue extends Inject
{
    /**
     * 获取注入值的真实值
     */
    public function getRealValue(): mixed
    {
        /** @var \Imi\Queue\Service\QueueService $imiQueue */
        $imiQueue = App::getBean('imiQueue');

        return $imiQueue->getQueue($this->name);
    }
}
