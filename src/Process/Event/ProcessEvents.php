<?php

declare(strict_types=1);

namespace Imi\Process\Event;

use Imi\Util\Traits\TStaticClass;

final class ProcessEvents
{
    use TStaticClass;

    /**
     * 自定义进程开始.
     */
    public const PROCESS_BEGIN = 'IMI.PROCESS.BEGIN';

    /**
     * 自定义进程结束.
     */
    public const PROCESS_END = 'IMI.PROCESS.END';
}
