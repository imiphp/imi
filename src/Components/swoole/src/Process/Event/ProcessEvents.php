<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Event;

use Imi\Util\Traits\TStaticClass;

final class ProcessEvents
{
    use TStaticClass;

    public const PIPE_MESSAGE = 'imi.process.pipe_message';
}
