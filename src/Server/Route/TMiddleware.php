<?php

namespace Imi\Server\Route;

use Imi\Config;

trait TMiddleware
{
    /**
     * 获取真实的中间件列表.
     *
     * @return string[]
     */
    protected function getMiddlewares($middlewares, $serverName)
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
