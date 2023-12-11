<?php

declare(strict_types=1);

namespace Imi\Queue\Event;

use Imi\Util\Traits\TStaticClass;

final class QueueEvents
{
    use TStaticClass;

    public const BEFORE_CONSUME = 'imi.queue.consumer.before_consume';

    public const AFTER_CONSUME = 'imi.queue.consumer.after_consume';

    public const BEFORE_POP = 'imi.queue.consumer.before_pop';

    public const AFTER_POP = 'imi.queue.consumer.after_pop';
}
