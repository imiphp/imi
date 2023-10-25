<?php

declare(strict_types=1);

namespace Imi\Queue\Facade;

use Imi\Facade\BaseFacade;

/**
 * @method static array                           getList()
 * @method static \Imi\Queue\Service\QueueService setList(array $list)
 * @method static \Imi\Queue\Model\QueueConfig    getQueueConfig(?string $name = NULL)
 * @method static \Imi\Queue\Driver\IQueueDriver  getQueue(?string $name = NULL)
 */
#[
    \Imi\Facade\Annotation\Facade(class: 'imiQueue')
]
class Queue extends BaseFacade
{
}
