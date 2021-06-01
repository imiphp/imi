<?php

declare(strict_types=1);

namespace Imi\Queue\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Parser;

/**
 * 注入队列对象
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
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
