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
     *
     * @todo 如 monolog 支持用户接管或者配置 logDepth，请更改实现方式
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        if (version_compare(InstalledVersions::getVersion('monolog/monolog'), '2.6.0', '>='))
        {
            $ref = new \ReflectionClass(parent::class);
            $property = $ref->getProperty('logDepth');
            $property->setAccessible(true);
            $property->setValue($this, \PHP_INT_MIN);
        }
    }
}
