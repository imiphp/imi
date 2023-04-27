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
        if (method_exists($this, 'useLoggingLoopDetection'))
        {
            $this->useLoggingLoopDetection(false);
        }
        elseif (version_compare(InstalledVersions::getVersion('monolog/monolog'), '2.6.0', '>='))
        {
            $ref = new \ReflectionClass(parent::class);
            $property = $ref->getProperty('logDepth');
            $property->setAccessible(true);
            $property->setValue($this, \PHP_INT_MIN);
        }
    }
}
