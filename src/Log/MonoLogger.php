<?php

declare(strict_types=1);

namespace Imi\Log;

use Composer\InstalledVersions;
use Monolog\Logger;

class MonoLogger extends Logger
{
    /**
     * {@inheritDoc}
     *
     * @param mixed $args
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);

        // 禁用不兼容协程并发的深度检测功能
        $this->useLoggingLoopDetection(false);
    }
}
