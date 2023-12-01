<?php

declare(strict_types=1);

namespace Imi\Queue\Event;

use Imi\Util\Traits\TStaticClass;

final class QueueEvents
{
    use TStaticClass;

    public const BEFORE_CONSUME = 'IMI.QUEUE.CONSUMER.BEFORE_CONSUME';

    public const AFTER_CONSUME = 'IMI.QUEUE.CONSUMER.AFTER_CONSUME';

    public const BEFORE_POP = 'IMI.QUEUE.CONSUMER.BEFORE_POP';

    public const AFTER_POP = 'IMI.QUEUE.CONSUMER.AFTER_POP';
}
