<?php

declare(strict_types=1);

namespace Imi\Queue\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade(class="imiQueue")
 *
 * @method static array getList()
 * @method static self setList(array $list)
 * @method static \Imi\Queue\Model\QueueConfig getQueueConfig(?string $name = NULL)
 * @method static \Imi\Queue\Driver\IQueueDriver getQueue(?string $name = NULL)
 */
abstract class Queue extends BaseFacade
{
}
