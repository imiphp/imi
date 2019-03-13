<?php

use Imi\RequestContext;

/**
 * 启动一个协程，自动创建和销毁上下文
 *
 * @param callable $callable
 * @param mixed $args
 * @return void
 */
function imigo(callable $callable, ...$args)
{
    return go(imiCallable($callable)(...$args));
}

/**
 * 为传入的回调自动创建和销毁上下文，并返回新的回调
 *
 * @param callable $callable
 * @return callable
 */
function imiCallable(callable $callable)
{
    return function(...$args) use($callable){
        try {
            RequestContext::create();
            return $callable(...$args);
        } finally {
            RequestContext::destroy();
        }
    };
}
