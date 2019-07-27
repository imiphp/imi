<?php

use Imi\RequestContext;
use Imi\App;
use Imi\ServerManage;

/**
 * 启动一个协程，自动创建和销毁上下文
 *
 * @param callable $callable
 * @param mixed $args
 * @return void
 */
function imigo(callable $callable, ...$args)
{
    $newCallable = imiCallable($callable);
    return go(function(...$args) use($newCallable){
        $newCallable(...$args);
    }, ...$args);
}

/**
 * 为传入的回调自动创建和销毁上下文，并返回新的回调
 *
 * @param callable $callable
 * @param boolean $withGo 是否内置启动一个协程，如果为true，则无法获取回调返回值
 * @return callable
 */
function imiCallable(callable $callable, bool $withGo = false)
{
    $server = RequestContext::exists() ? RequestContext::get('server') : null;
    $resultCallable = function(...$args) use($callable, $server){
        if(!RequestContext::exists())
        {
            RequestContext::create();
            RequestContext::set('server', $server);
        }
        return $callable(...$args);
    };
    if($withGo)
    {
        return function(...$args) use($resultCallable){
            return go(function(...$args) use($resultCallable){
                return $resultCallable(...$args);
            }, ...$args);
        };
    }
    else
    {
        return $resultCallable;
    }
}
