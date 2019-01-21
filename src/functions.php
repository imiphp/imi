<?php

use Imi\RequestContext;

/**
 * 启动一个协程，自动创建和销毁上下文
 *
 * @param callable $callable
 * @return void
 */
function imigo(callable $callable)
{
    return go(function() use($callable){
        try {
            RequestContext::create();
            return $callable();
        } finally {
            RequestContext::destroy();
        }
    });
}
