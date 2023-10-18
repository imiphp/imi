<?php

declare(strict_types=1);

namespace Imi\Log;

use Monolog\Logger;

/**
 * @phpstan-ignore-next-line
 */
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

        $this->useLoggingLoopDetection(false);
    }
}
