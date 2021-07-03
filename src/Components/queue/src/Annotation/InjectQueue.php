<?php

declare(strict_types=1);

namespace Imi\Queue\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Inherit;

/**
 * 注入队列对象
 *
 * @Inherit
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute]
class InjectQueue extends Inject
{
    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        /** @var \Imi\Queue\Service\QueueService $imiQueue */
        $imiQueue = App::getBean('imiQueue');

        return $imiQueue->getQueue($this->name);
    }
}
