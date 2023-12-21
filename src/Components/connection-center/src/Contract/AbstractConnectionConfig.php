<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

use Imi\Util\Uri;

abstract class AbstractConnectionConfig implements IConnectionConfig
{
    public function __construct(protected float $weight = 0)
    {
    }

    public static function create(string|array $config): self
    {
        if (\is_string($config))
        {
            $config = static::parseStringConfig($config);
        }

        return static::__create($config);
    }

    public static function parseStringConfig(string $string): array
    {
        $uriObj = new Uri($string);
        parse_str($uriObj->getQuery(), $config);
        $config['host'] ??= $uriObj->getHost();
        $config['port'] ??= $uriObj->getPort();

        return $config;
    }

    /**
     * 权重.
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    abstract protected static function __create(array $config): self;
}
