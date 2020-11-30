<?php

declare(strict_types=1);

namespace Imi\Server\Route;

use Imi\Config;

trait TMiddleware
{
    /**
     * 获取真实的中间件列表.
     *
     * @param string|string[] $middlewares
     * @param string          $serverName
     *
     * @return array
     */
    protected function getMiddlewares($middlewares, string $serverName): array
    {
        if (\is_array($middlewares))
        {
            return $middlewares;
        }
        elseif (isset($middlewares[0]) && '@' === $middlewares[0])
        {
            return Config::get('@server.' . $serverName . '.middleware.groups.' . substr($middlewares, 1), []);
        }
        else
        {
            return [$middlewares];
        }
    }
}
